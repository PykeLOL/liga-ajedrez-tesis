<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Permiso;
use App\Models\TipoIdentificacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    private $rol;
    private $tipoIdentificacion;
    private $usuario;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear datos base
        $this->rol = Rol::factory()->create();
        $this->tipoIdentificacion = TipoIdentificacion::factory()->create();

        // Crear usuario autenticado
        $this->usuario = Usuario::factory()
            ->withRol($this->rol->id)
            ->withTipoIdentificacion($this->tipoIdentificacion->id)
            ->create();

        // Asignar permisos necesarios
        $this->asignarPermisoARol($this->rol, 'crear', 'usuarios');
        $this->asignarPermisoARol($this->rol, 'ver', 'usuarios');
        $this->asignarPermisoARol($this->rol, 'editar', 'usuarios');

        // Autenticación
        $this->actingAs($this->usuario, 'api');
    }

    /**
     * Test: crear usuario correctamente
     */
    public function test_se_puede_crear_usuario_correctamente()
    {
        $response = $this->postJson('/api/usuarios', [
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'tipo_identificacion_id' => $this->tipoIdentificacion->id,
            'numero_identificacion' => '123456789',
            'email' => 'juan@test.com',
            'contraseña' => '12345678',
            'confirmar_contraseña' => '12345678',
            'estado' => true,
            'rol_id' => $this->rol->id
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'usuario' => ['id', 'nombre', 'email']
                 ]);

        $this->assertDatabaseHas('usuarios', [
            'email' => 'juan@test.com'
        ]);
    }

    /**
     * Test: validación de campos
     */
    public function test_falla_creacion_usuario_si_faltan_campos()
    {
        $response = $this->postJson('/api/usuarios', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'nombre',
                     'email',
                     'contraseña'
                 ]);
    }

    /**
     * Test: listar usuarios
     */
    public function test_se_pueden_listar_usuarios()
    {
        Usuario::factory()
            ->withRol($this->rol->id)
            ->withTipoIdentificacion($this->tipoIdentificacion->id)
            ->create();

        $response = $this->getJson('/api/usuarios');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    '*' => ['id', 'nombre', 'email']
                 ]);
    }

    /**
     * Test: ver detalles de un usuario
     */
    public function test_se_pueden_ver_detalles_de_un_usuario()
    {
        // Crear un nuevo usuario sin permisos
        $usuario = Usuario::factory()
            ->withRol($this->rol->id)
            ->withTipoIdentificacion($this->tipoIdentificacion->id)
            ->create();

        $response = $this->getJson('/api/usuarios/' . $usuario->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id', 'nombre', 'email'
                 ]);
    }
}
