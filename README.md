# Aspirantes CIMAT

Aplicación web para el **registro, evaluación y consulta de aspirantes** de un
proceso de selección. Construida con Laravel 13, PHP 8.5, MariaDB 11.8 y
empaquetada en Docker para ejecutarse con un solo comando.

---

## Contenido

- [Funcionalidades](#funcionalidades)
- [Stack tecnológico](#stack-tecnológico)
- [Requisitos](#requisitos)
- [Inicio rápido](#inicio-rápido)
- [Servicios y puertos](#servicios-y-puertos)
- [Datos de demostración](#datos-de-demostración)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Base de datos](#base-de-datos)
- [Rutas de la aplicación](#rutas-de-la-aplicación)
- [Comandos útiles](#comandos-útiles)
- [Pruebas y estilo de código](#pruebas-y-estilo-de-código)
- [Configuración](#configuración)
- [Seguridad](#seguridad)
- [Solución de problemas](#solución-de-problemas)
- [Decisiones técnicas](#decisiones-técnicas)

---

## Funcionalidades

### A. Registro de aspirantes
Formulario con validación en cliente y servidor para capturar:

- Nombre completo
- Puesto al que aplica
- Estado civil (soltero, casado, divorciado, viudo, unión libre)
- Fecha de nacimiento (anterior a hoy, posterior a 1900)
- Carrera o formación académica

### B. Evaluación de aspirantes
Una evaluación por aspirante (relación 1:1) con:

- Conocimientos técnicos (0–10, hasta 2 decimales)
- Referencias (0–10, hasta 2 decimales)
- Habilidades blandas (0–10, hasta 2 decimales)
- Comentarios generales (texto libre, opcional, máx. 2000 caracteres)

Las calificaciones se validan en la aplicación **y** en la base de datos
(restricciones `CHECK`). La evaluación puede crearse y editarse.

### C. Consulta de aspirantes
- Listado con búsqueda por nombre/puesto/carrera, filtro por estado civil y
  paginación.
- Ficha con la información general, edad calculada y promedio.
- Panel de evaluación con barras por criterio, promedio y comentarios.

---

## Stack tecnológico

| Componente | Versión |
|---|---|
| Laravel | 13.39 |
| PHP | 8.5 (FPM, Alpine) |
| MariaDB | 11.8 LTS |
| Nginx | 1.30 (Alpine) |
| Tailwind CSS + Vite | 4.x / 8.x |
| PHPUnit | 12.x |

Las versiones de PHP y Laravel cuentan con soporte activo. El detalle de las
decisiones está en [docs/DECISIONES.md](docs/DECISIONES.md).

---

## Requisitos

- **Docker** 24 o superior
- **Docker Compose** v2.20 o superior (`docker compose`, no `docker-compose`)
- Puertos libres: **8090** (aplicación) y opcionalmente **8091** (Adminer)

No es necesario instalar PHP, Composer ni Node en el equipo: todo se construye
dentro de los contenedores.

---

## Inicio rápido

```bash
# 1. (Opcional pero recomendado) Crear el archivo de configuración
cp .env.example .env

# 2. Construir y levantar la aplicación
docker compose up -d --build

# 3. Abrir en el navegador
#    http://localhost:8090
```

La aplicación también arranca **sin** crear el archivo `.env`, ya que
`compose.yaml` define valores por defecto para todas las variables.

Comprobar que todo está listo:

```bash
docker compose ps          # los tres servicios deben estar "healthy"
curl -I http://localhost:8090/up
```

> El arranque aplica las migraciones y carga los datos de demostración
> automáticamente. La primera construcción tarda unos minutos.

---

## Servicios y puertos

| Servicio | Descripción | Puerto |
|---|---|---|
| `web` | Nginx, punto de entrada HTTP | `8090` → 80 |
| `app` | PHP-FPM (Laravel) | interno `9000` |
| `db` | MariaDB 11.8 | interno `3306` (no expuesto) |
| `tests` | Contenedor efímero para la suite de pruebas | — |
| `adminer` | Administrador de BD opcional (perfil `tools`) | `8091` → 8080 |

Para usar Adminer:

```bash
docker compose --profile tools up -d
# http://localhost:8091
# Servidor: db · Usuario: aspirantes · Contraseña: la definida en .env
```

Para cambiar el puerto de la aplicación: ajusta `APP_PORT` en `.env`.

---

## Datos de demostración

El seeder es **idempotente** y crea 8 aspirantes, 6 de ellos con evaluación.
Se ejecuta en cada arranque si `SEED_DB=true` (valor por defecto).

Para arrancar sin datos de demostración:

```bash
SEED_DB=false docker compose up -d
```

o bien define `SEED_DB=false` en `.env` antes de levantar los contenedores.

---

## Estructura del proyecto

```
.
├── compose.yaml                 # Orquestación: web, app, db, tests, adminer
├── .env.example                 # Variables de Compose
├── .dockerignore
├── Makefile                     # Atajos de operación
├── README.md
├── docs/
│   └── DECISIONES.md            # Tecnologías y decisiones técnicas
├── docker/
│   ├── php/
│   │   ├── Dockerfile           # Multi-stage: vendor, assets, runtime, testing, web
│   │   ├── entrypoint.sh        # Espera BD, APP_KEY, migraciones, cachés
│   │   ├── php.ini              # OPcache y límites de producción
│   │   └── www.conf             # Pool de PHP-FPM
│   └── nginx/
│       └── default.conf
└── src/                         # Aplicación Laravel
    ├── app/
    │   ├── Enums/EstadoCivil.php
    │   ├── Http/Controllers/    # AspiranteController, EvaluacionController
    │   ├── Http/Middleware/     # SecurityHeaders
    │   ├── Http/Requests/       # Validación de alta y evaluación
    │   └── Models/              # Aspirante, Evaluacion
    ├── database/
    │   ├── migrations/          # Estructura de la BD
    │   ├── factories/
    │   └── seeders/
    ├── resources/
    │   ├── css/app.css
    │   └── views/               # Blade + componentes
    ├── routes/web.php
    ├── lang/es/                 # Mensajes en español
    └── tests/                   # 25 pruebas (Unit + Feature)
```

---

## Base de datos

### `aspirantes`

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | BIGINT UNSIGNED | PK, autoincremental |
| `nombre_completo` | VARCHAR(150) | NOT NULL |
| `puesto` | VARCHAR(120) | NOT NULL, índice |
| `estado_civil` | VARCHAR(20) | NOT NULL, índice |
| `fecha_nacimiento` | DATE | NOT NULL |
| `carrera` | VARCHAR(150) | NOT NULL |
| `created_at`, `updated_at` | TIMESTAMP | — |

### `evaluaciones`

| Columna | Tipo | Restricciones |
|---|---|---|
| `id` | BIGINT UNSIGNED | PK, autoincremental |
| `aspirante_id` | BIGINT UNSIGNED | FK → `aspirantes.id`, **UNIQUE**, `ON DELETE CASCADE` |
| `conocimientos_tecnicos` | DECIMAL(4,2) | NOT NULL, `CHECK BETWEEN 0 AND 10` |
| `referencias` | DECIMAL(4,2) | NOT NULL, `CHECK BETWEEN 0 AND 10` |
| `habilidades_blandas` | DECIMAL(4,2) | NOT NULL, `CHECK BETWEEN 0 AND 10` |
| `comentarios` | TEXT | NULL |
| `created_at`, `updated_at` | TIMESTAMP | — |

El `UNIQUE` en `aspirante_id` garantiza la relación 1:1 a nivel de BD. El
promedio se calcula en la aplicación (no se almacena) para evitar datos
derivados inconsistentes.

Las migraciones están en `src/database/migrations/` y se ejecutan
automáticamente al arrancar el contenedor.

---

## Rutas de la aplicación

| Método | Ruta | Acción |
|---|---|---|
| GET | `/` | Redirige a `/aspirantes` |
| GET | `/aspirantes` | Listado con búsqueda y filtros |
| GET | `/aspirantes/crear` | Formulario de registro |
| POST | `/aspirantes` | Guarda un aspirante |
| GET | `/aspirantes/{id}` | Ficha del aspirante |
| GET | `/aspirantes/{id}/evaluacion/crear` | Formulario de evaluación |
| POST | `/aspirantes/{id}/evaluacion` | Registra la evaluación |
| GET | `/aspirantes/{id}/evaluacion/editar` | Edición de la evaluación |
| PUT | `/aspirantes/{id}/evaluacion` | Actualiza la evaluación |

---

## Comandos útiles

Con `make`:

```bash
make help       # lista los comandos disponibles
make up         # construye y levanta la aplicación
make down       # detiene los contenedores (conserva la BD)
make logs       # sigue los logs de app y web
make shell      # shell dentro del contenedor de la aplicación
make db-shell   # cliente de MariaDB
make migrate    # migraciones pendientes
make seed       # datos de demostración
make test       # suite de pruebas
make lint       # verifica estilo con Pint
make fresh      # reinicia desde cero (BORRA la BD)
```

Sin `make`:

```bash
docker compose up -d --build
docker compose logs -f app web
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
docker compose --profile testing run --rm tests
docker compose down          # conserva los volúmenes
docker compose down -v       # elimina también la base de datos
```

---

## Pruebas y estilo de código

```bash
# 25 pruebas: validaciones, rango de calificaciones, relación 1:1, consultas
docker compose --profile testing run --rm tests

# Estilo (Laravel Pint, PSR-12)
docker compose --profile testing run --rm tests vendor/bin/pint --test
```

Las pruebas usan SQLite en memoria y un contenedor aislado, por lo que no
afectan la base de datos de la aplicación.

---

## Configuración

Variables principales (ver `.env.example`):

| Variable | Valor por defecto | Descripción |
|---|---|---|
| `APP_PORT` | `8090` | Puerto público de la aplicación |
| `APP_NAME` | `Aspirantes CIMAT` | Nombre mostrado |
| `APP_ENV` | `production` | Entorno de Laravel |
| `APP_DEBUG` | `false` | Depuración (mantener en `false`) |
| `APP_KEY` | vacío | Si está vacío se genera una clave efímera al arrancar |
| `APP_URL` | `http://localhost:8090` | URL base |
| `DB_DATABASE` | `aspirantes` | Nombre de la base de datos |
| `DB_USERNAME` | `aspirantes` | Usuario de la aplicación |
| `DB_PASSWORD` | `aspirantes_secret` | Contraseña (cambiar en producción) |
| `DB_ROOT_PASSWORD` | `aspirantes_root_secret` | Contraseña de root (cambiar) |
| `SEED_DB` | `true` | Carga datos de demostración |
| `LOG_LEVEL` | `warning` | Nivel de log |

Para fijar una `APP_KEY` permanente:

```bash
docker compose exec app php artisan key:generate --show
# Copia el valor en APP_KEY dentro de .env y reinicia: docker compose up -d
```

---

## Seguridad

- Validación de todos los datos en el servidor mediante `FormRequest`.
- Protección CSRF en todos los formularios (middleware web de Laravel).
- Escapado automático de salida con Blade (`{{ }}`).
- Asignación masiva controlada (`$fillable`) y consultas parametrizadas por Eloquent.
- Límite de peticiones (`throttle:30,1`) en las rutas de escritura.
- Cabeceras de seguridad: CSP, `X-Frame-Options`, `X-Content-Type-Options`,
  `Referrer-Policy` y `Permissions-Policy` (middleware propio).
- Contenedores sin privilegios (`www-data`, `nginx`) y sistema de archivos de
  solo lectura con volúmenes/tmpfs para lo que requiere escritura.
- La base de datos no expone puertos al host; solo es accesible en la red interna.
- Errores de aplicación ocultos (`APP_DEBUG=false`).
- Secretos fuera del repositorio: `.env` está en `.gitignore`.

---

## Solución de problemas

**El puerto 8090 está ocupado**
Cambia `APP_PORT` en `.env` y ejecuta `docker compose up -d`.

**Los contenedores no arrancan o la app no responde**

```bash
docker compose ps
docker compose logs -f app web db
```

**Reiniciar la base de datos desde cero**

```bash
docker compose down -v
docker compose up -d --build
```

**Reconstruir tras cambiar código o dependencias**

```bash
docker compose build --no-cache
docker compose up -d
```

**`db` aparece como *unhealthy***
Espera 30–60 segundos en el primer arranque (inicialización de MariaDB). Si
persiste, revisa `docker compose logs db`.

**Permisos en Linux**
Los contenedores escriben únicamente en volúmenes nombrados
(`examen-cimat-dbdata`, `examen-cimat-storage`), por lo que no hay problemas de
propietarios en el código fuente.

---

## Decisiones técnicas

El resumen de tecnologías, arquitectura y alternativas descartadas está en
[docs/DECISIONES.md](docs/DECISIONES.md).

---

Proyecto desarrollado como examen práctico para CIMAT.
