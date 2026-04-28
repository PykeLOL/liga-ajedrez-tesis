<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rol;
use App\Models\Permiso;
use App\Models\Usuario;
use App\Models\TipoIdentificacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermisoTest extends TestCase
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

        // Permisos necesarios para el módulo permisos
        $this->asignarPermisoARol($this->rol, 'crear', 'permisos');
        $this->asignarPermisoARol($this->rol, 'ver', 'permisos');
        $this->asignarPermisoARol($this->rol, 'editar', 'permisos');
        $this->asignarPermisoARol($this->rol, 'eliminar', 'permisos');

        $this->actingAs($this->usuario, 'api');
    }

    /**
     * Test: crear permiso
     */
    public function test_se_puede_crear_permiso()
    {
        $tipoAccionId = DB::table('tipo_accion')->insertGetId([
            'nombre' => 'crear',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $moduloId = DB::table('modulos')->insertGetId([
            'nombre' => 'test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson('/api/permisos', [
            'nombre' => 'crear-test',
            'descripcion' => 'Permiso de prueba',
            'tipo_accion_id' => $tipoAccionId,
            'modulo_id' => $moduloId,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('permisos', [
            'nombre' => 'crear-test'
        ]);
    }

    /**
     * Test: listar permisos
     */
    public function test_se_pueden_listar_permisos()
    {
        $tipoAccionId = DB::table('tipo_accion')->insertGetId([
            'nombre' => 'ver',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $moduloId = DB::table('modulos')->insertGetId([
            'nombre' => 'usuarios',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Permiso::create([
            'nombre' => 'ver-usuarios',
            'descripcion' => 'Permiso ver usuarios',
            'tipo_accion_id' => $tipoAccionId,
            'modulo_id' => $moduloId,
        ]);

        $response = $this->getJson('/api/permisos');

        $response->assertStatus(200);
    }

    /**
     * Test: ver permiso
     */
    public function test_se_puede_ver_un_permiso()
    {
        $tipoAccionId = DB::table('tipo_accion')->insertGetId([
            'nombre' => 'editar',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $moduloId = DB::table('modulos')->insertGetId([
            'nombre' => 'usuarios',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $permiso = Permiso::create([
            'nombre' => 'editar-usuarios',
            'descripcion' => 'Permiso editar usuarios',
            'tipo_accion_id' => $tipoAccionId,
            'modulo_id' => $moduloId,
        ]);

        $response = $this->getJson('/api/permisos/' . $permiso->id);

        $response->assertStatus(200);
    }

    /**
     * Test: eliminar permiso
     */
    public function test_se_puede_eliminar_permiso()
    {
        $tipoAccionId = DB::table('tipo_accion')->insertGetId([
            'nombre' => 'eliminar',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $moduloId = DB::table('modulos')->insertGetId([
            'nombre' => 'usuarios',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $permiso = Permiso::create([
            'nombre' => 'eliminar-usuarios',
            'descripcion' => 'Permiso eliminar usuarios',
            'tipo_accion_id' => $tipoAccionId,
            'modulo_id' => $moduloId,
        ]);

        $response = $this->deleteJson('/api/permisos/' . $permiso->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('permisos', [
            'id' => $permiso->id
        ]);
    }
}
