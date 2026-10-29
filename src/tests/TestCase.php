<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Las pruebas no compilan assets: se omiten las etiquetas @vite para
        // que no dependan de public/build/manifest.json. El build real de
        // frontend se valida en el job de Docker del CI.
        $this->withoutVite();
    }
}
