<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rol;
use App\Models\Liga;
use App\Models\Usuario;
use App\Models\TipoIdentificacion;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LigaTest extends TestCase
{
    use RefreshDatabase;

    private $rol;
    private $usuario;
    private $tipoIdentificacion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rol = Rol::factory()->create();
        $this->tipoIdentificacion = TipoIdentificacion::factory()->create();

        $this->usuario = Usuario::factory()
            ->withRol($this->rol->id)
            ->withTipoIdentificacion($this->tipoIdentificacion->id)
            ->create();

        // Permisos
        $this->asignarPermisoARol($this->rol, 'crear', 'ligas');
        $this->asignarPermisoARol($this->rol, 'ver', 'ligas');
        $this->asignarPermisoARol($this->rol, 'editar', 'ligas');
        $this->asignarPermisoARol($this->rol, 'eliminar', 'ligas');

        $this->actingAs($this->usuario, 'api');
    }

    /**
     * Crear liga
     */
    public function test_se_puede_crear_liga()
    {
        $response = $this->postJson('/api/ligas', [
            'nombre' => 'Liga Meta',
            'descripcion' => 'Liga de prueba'
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('ligas', [
            'nombre' => 'Liga Meta'
        ]);
    }

    /**
     * Listar ligas
     */
    public function test_se_pueden_listar_ligas()
    {
        Liga::factory()->create();

        $response = $this->getJson('/api/ligas');

        $response->assertStatus(200);
    }

    /**
     * Ver una liga
     */
    public function test_se_puede_ver_una_liga()
    {
        $liga = Liga::factory()->create();

        $response = $this->getJson('/api/ligas/' . $liga->id);

        $response->assertStatus(200);
    }

    /**
     * Actualizar liga
     */
    public function test_se_puede_actualizar_liga()
    {
        $liga = Liga::factory()->create();

        $response = $this->putJson('/api/ligas/' . $liga->id, [
            'nombre' => 'Liga Actualizada',
            'descripcion' => $liga->descripcion
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('ligas', [
            'nombre' => 'Liga Actualizada'
        ]);
    }

    /**
     * Eliminar liga
     */
    public function test_se_puede_eliminar_liga()
    {
        $liga = Liga::factory()->create();

        $response = $this->deleteJson('/api/ligas/' . $liga->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('ligas', [
            'id' => $liga->id
        ]);
    }
}
