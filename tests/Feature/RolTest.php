<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\TipoIdentificacion;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolTest extends TestCase
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

        // Permisos necesarios
        $this->asignarPermisoARol($this->rol, 'crear', 'roles');
        $this->asignarPermisoARol($this->rol, 'ver', 'roles');
        $this->asignarPermisoARol($this->rol, 'editar', 'roles');
        $this->asignarPermisoARol($this->rol, 'eliminar', 'roles');

        $this->actingAs($this->usuario, 'api');
    }

    /**
     * Test: crear rol
     */
    public function test_se_puede_crear_rol()
    {
        $response = $this->postJson('/api/roles', [
            'nombre' => 'Entrenador',
            'descripcion' => 'Rol de entrenador'
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('roles', [
            'nombre' => 'Entrenador'
        ]);
    }

    /**
     * Test: listar roles
     */
    public function test_se_pueden_listar_roles()
    {
        Rol::factory()->create();

        $response = $this->getJson('/api/roles');

        $response->assertStatus(200);
    }

    /**
     * Test: ver rol
     */
    public function test_se_puede_ver_un_rol()
    {
        $rol = Rol::factory()->create();

        $response = $this->getJson('/api/roles/' . $rol->id);

        $response->assertStatus(200);
    }

    /**
     * Test: actualizar rol
     */
    public function test_se_puede_actualizar_rol()
    {
        $rol = Rol::factory()->create();

        $response = $this->putJson('/api/roles/' . $rol->id, [
            'nombre' => 'Administrador'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('roles', [
            'nombre' => 'Administrador'
        ]);
    }

    /**
     * Test: eliminar rol
     */
    public function test_se_puede_eliminar_rol()
    {
        $rol = Rol::factory()->create();

        $response = $this->deleteJson('/api/roles/' . $rol->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('roles', [
            'id' => $rol->id
        ]);
    }
}
