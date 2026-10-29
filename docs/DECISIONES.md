# Tecnologías y decisiones técnicas

Documento complementario al [README](../README.md). Explica **qué** se usó y
**por qué**, así como las alternativas evaluadas.

---

## 1. Stack

| Capa | Tecnología | Versión | Motivo |
|---|---|---|---|
| Framework | Laravel | 13.39 | Versión estable más reciente con soporte activo; ecosistema maduro, validación y ORM integrados |
| Lenguaje | PHP | 8.5 | Versión con soporte activo; Laravel 13 requiere `^8.3` |
| Base de datos | MariaDB | 11.8 LTS | LTS vigente, compatible con MySQL, imagen oficial ligera y soporte de `CHECK` |
| Servidor web | Nginx | 1.30 Alpine | Sirve estáticos y delega PHP a FPM; imagen mínima |
| Runtime PHP | PHP-FPM | 8.5 Alpine | Procesos persistentes, OPcache y pool ajustado |
| Frontend | Blade + Tailwind CSS 4 + Vite 8 | — | Sin CDN: los assets se compilan en la imagen y se sirven localmente |
| Pruebas | PHPUnit | 12.x | Incluido por el esqueleto de Laravel; 25 pruebas |
| Estilo | Laravel Pint | — | PSR-12 y convenciones de Laravel |
| Orquestación | Docker Compose | v2+ | Un solo comando para construir y ejecutar |

No se usó Laravel Sail porque el examen pide explícitamente `Dockerfile` y
`compose.yaml` propios; Sail añade una capa de abstracción innecesaria.

---

## 2. Arquitectura de contenedores

```
 navegador
    │  :8090
    ▼
┌─────────────┐   fastcgi :9000   ┌─────────────┐
│  web        │ ────────────────► │  app        │
│  nginx      │                   │  php-fpm    │
└─────────────┘                   └──────┬──────┘
                                         │ mysql :3306
                                         ▼
                                  ┌─────────────┐
                                  │  db         │
                                  │  mariadb    │
                                  └─────────────┘
                        red interna: examen-cimat_internal
```

**Decisiones:**

- **Separación `web` / `app`**: Nginx sirve assets estáticos y PHP-FPM procesa
  la aplicación. Cada contenedor tiene una sola responsabilidad y puede
  escalarse o reiniciarse por separado.
- **Sin volúmenes de código**: el código se copia en la imagen. Esto garantiza
  que la aplicación funcione igual en cualquier equipo y evita problemas de
  permisos o de rendimiento de bind mounts. El único volumen de la app es
  `storage/` (logs y caché de vistas).
- **Dockerfile multi-stage**: se compilan assets con Node, se instalan
  dependencias con Composer y el runtime final solo contiene lo necesario.
  Las capas de build no llegan a la imagen de producción.
- **Nginx con su propia copia de `public/`**: evita compartir un volumen de
  código entre contenedores; los assets son deterministas y de solo lectura.
- **`read_only: true` + `tmpfs`**: el sistema de archivos de `app` y `web` es de
  solo lectura; solo son escribibles `storage/` (volumen), `bootstrap/cache` y
  `/tmp` (tmpfs). Reduce la superficie de ataque.
- **Usuario sin privilegios**: PHP-FPM corre como `www-data` y Nginx como
  `nginx`; no se ejecuta nada como root dentro de la aplicación.
- **Base de datos no expuesta**: `db` no publica puertos al host; solo es
  accesible por la red interna de Compose.
- **Healthchecks**: `db` usa `healthcheck.sh --connect`; `app` valida que
  Artisan arranque; `web` consulta `/up`. `depends_on` con
  `condition: service_healthy` evita arranques prematuros.
- **Puerto 8090**: se eligió para evitar conflictos con el 8080, que suele estar
  ocupado por otros servicios. Es configurable con `APP_PORT`.

**Contenedor de pruebas separado**: `tests` reutiliza el runtime, añade las
dependencias de desarrollo y ejecuta PHPUnit con SQLite en memoria. Así la
imagen de producción no incluye PHPUnit ni Faker.

---

## 3. Modelo de datos

**Relación 1:1 (`aspirantes` ↔ `evaluaciones`)**

- El enunciado pide registrar *una* evaluación por aspirante y consultar *su*
  evaluación; el modelo 1:1 lo representa directamente.
- Se impone con un índice **UNIQUE** en `evaluaciones.aspirante_id` y
  `ON DELETE CASCADE`.
- La aplicación evita el error 500 si se intenta una segunda evaluación:
  redirige a la edición con un mensaje informativo y, además, captura la
  violación del índice único (`QueryException`) ante solicitudes concurrentes.

**Tipos y restricciones**

- `DECIMAL(4,2)` para las calificaciones: admite valores como `8.75` y evita
  errores de coma flotante.
- `CHECK BETWEEN 0 AND 10` en cada criterio: segunda barrera independiente de la
  validación de Laravel. Se comprobó que MariaDB rechaza un `INSERT` con `11`.
- `VARCHAR` + enum de PHP para `estado_civil`: portabilidad y etiquetas en
  español sin depender del `ENUM` del motor.
- Índices en `puesto` y `estado_civil` por ser columnas de filtro.

**Promedio calculado, no almacenado**: se obtiene con `Evaluacion::promedio()`.
Guardarlo duplicaría información y podría quedar desincronizado si se edita la
evaluación.

**Seeder idempotente**: usa `firstOrCreate` / `updateOrCreate`, por lo que puede
ejecutarse en cada arranque sin duplicar registros.

---

## 4. Validación y manejo de errores

- **FormRequests** (`StoreAspiranteRequest`, `StoreEvaluacionRequest`): reglas
  declarativas, mensajes y nombres de campo en español.
- **Doble validación**: HTML (`required`, `min`, `max`, `step`, `type=number`) y
  servidor (fuente de verdad).
- **Calificaciones**: `numeric|between:0,10|decimal:0,2`. Se rechazan negativos,
  valores mayores a 10 y más de dos decimales.
- **Nombre**: regex unicode que admite letras, espacios, apóstrofes, puntos y
  guiones; bloquea dígitos y etiquetas HTML.
- **Fechas**: `date|after:1900-01-01|before:today`.
- **Estado civil**: `Rule::enum`, por lo que valores inventados se rechazan.
- **Mensajes en español** (`lang/es/validation.php`) y resumen de errores
  accesible al inicio de cada formulario (`role="alert"`).
- **Errores controlados**: el intento de duplicar una evaluación y los registros
  inexistentes (404 personalizado) tienen respuestas explícitas.

---

## 5. Seguridad

| Medida | Implementación |
|---|---|
| CSRF | Middleware `web` de Laravel + `@csrf` en todos los formularios |
| XSS | Escapado automático de Blade; sin `{!! !!}` con datos de usuario |
| Asignación masiva | `$fillable` explícito en ambos modelos |
| Inyección SQL | Eloquent y consultas parametrizadas |
| Fuerza bruta / abuso | `throttle:30,1` en POST y PUT |
| Cabeceras | `SecurityHeaders`: CSP, `X-Frame-Options: DENY`, `nosniff`, `Referrer-Policy`, `Permissions-Policy`, HSTS si hay HTTPS |
| Secretos | `.env` ignorado por Git; solo se versiona `.env.example` |
| Contenedores | Usuarios sin privilegios, filesystem de solo lectura, imágenes Alpine |
| Base de datos | Sin puerto público, usuario de aplicación con permisos sobre su esquema |
| Errores | `APP_DEBUG=false` en producción; no se filtran trazas |

**Sin autenticación**: el examen no la solicita y añadirla dificultaría la
evaluación. Si se requiriera, el siguiente paso sería Laravel Breeze/Fortify con
un usuario administrador.

---

## 6. Interfaz y experiencia de usuario

- Blade + Tailwind 4 compilado con Vite: sin dependencias de CDN, por lo que la
  interfaz funciona sin conexión a internet.
- Diseño responsive: tabla en escritorio y tarjetas en móvil.
- Componentes reutilizables (`x-form.input`, `x-form.select`,
  `x-form.textarea`, `x-score-bar`, `x-estado-evaluacion`).
- Accesibilidad: etiquetas asociadas, `aria-invalid`/`aria-describedby`, foco
  visible, enlace "Saltar al contenido", `role="alert"` en errores y
  `role="progressbar"` en las calificaciones.
- Idioma español, fechas `d/m/Y`, edad calculada y promedio con dos decimales.

---

## 7. Operación y despliegue

`docker/php/entrypoint.sh` hace el arranque reproducible:

1. Espera a que MariaDB acepte conexiones (hasta 60 s).
2. Genera `APP_KEY` si no se proporcionó (clave efímera del contenedor).
3. `package:discover` y `migrate --force`.
4. `db:seed --force` si `SEED_DB=true` (idempotente).
5. Limpia y regenera cachés de configuración, rutas y vistas.
6. `exec php-fpm` (el proceso 1 recibe las señales correctamente).

Ventajas: `docker compose up -d --build` es suficiente; no hay pasos manuales ni
modificaciones al código. Las migraciones se ejecutan solas y el seeder puede
desactivarse con una variable.

**Cachés**: `config:cache`, `route:cache` y `view:cache` se generan en el
arranque (no en el build) porque dependen de variables de entorno. OPcache está
activo con `validate_timestamps=0`; al cambiar código se reconstruye la imagen.

---

## 8. Pruebas

25 pruebas (86 aserciones) divididas en:

- **Registro**: alta válida, campos obligatorios, fecha futura, estado civil
  inválido, nombre con caracteres no permitidos, acceso al formulario.
- **Evaluación**: decimales, valores fuera de rango (−0.01, 10.01, 25), más de
  dos decimales, comentarios opcionales, unicidad 1:1, actualización.
- **Consulta**: listado, búsqueda, filtro por estado civil, ficha con y sin
  evaluación, 404, redirección de la raíz.
- **Unidad**: cálculo del promedio, criterios y etiquetas del enum.

Se usa **SQLite en memoria** por velocidad y aislamiento. Las restricciones
`CHECK` solo existen en MariaDB y se verificaron manualmente contra la base de
datos real; las pruebas de aplicación cubren la validación equivalente.

---

## 9. Alternativas descartadas

| Alternativa | Motivo de descarte |
|---|---|
| Laravel Sail | El examen exige Dockerfile y compose propios |
| MySQL 8.4 LTS | Igualmente válido; MariaDB es más ligero y su LTS está vigente |
| Relación 1:N (historial) | El enunciado plantea una evaluación por aspirante; se priorizó el requisito literal |
| Autenticación con Breeze | No solicitada; añade fricción a la evaluación |
| Bootstrap/Tailwind por CDN | Dependencia de internet en tiempo de ejecución |
| Un solo contenedor (nginx + FPM) | Peor separación de responsabilidades y escalabilidad |
| Guardar el promedio en BD | Dato derivado, riesgo de inconsistencia |
| `ENUM` de MySQL para estado civil | Menos portable y más rígido ante cambios |
| Tests contra MariaDB | Más lentos y acoplados; SQLite cubre la lógica de aplicación |

---

## 10. Limitaciones y mejoras futuras

- Sin edición ni borrado de aspirantes (fuera del alcance del enunciado).
- Sin historial de evaluaciones ni auditoría de cambios.
- Sin carga de CV ni exportación de reportes.
- `APP_KEY` efímera si no se define: válido para demo, pero en producción debe
  fijarse en `.env` o en un gestor de secretos.
- Posibles mejoras: autenticación con roles, API REST, paginación configurable,
  pruebas de navegador (Dusk/Playwright) y pipeline de despliegue.
