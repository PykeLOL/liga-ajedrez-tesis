<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\Categoria;
use App\Models\TipoIdentificacion;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoriaTest extends TestCase
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
        $this->asignarPermisoARol($this->rol, 'crear', 'categorias');
        $this->asignarPermisoARol($this->rol, 'ver', 'categorias');
        $this->asignarPermisoARol($this->rol, 'editar', 'categorias');
        $this->asignarPermisoARol($this->rol, 'eliminar', 'categorias');

        $this->actingAs($this->usuario, 'api');
    }

    /**
     * Test: crear categoria
     */
    public function test_se_puede_crear_categoria()
    {
        $response = $this->postJson('/api/categorias', [
            'nombre' => 'Sub-1800',
            'descripcion' => 'Categoría para jugadores con elo menor a 1800',
            'edad_minima' => 0,
            'edad_maxima' => 100,
            'estado' => true
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('categorias', [
            'nombre' => 'Sub-1800'
        ]);
    }

    /**
     * Test: listar categorias
     */
    public function test_se_pueden_listar_categorias()
    {
        Categoria::factory()->create();

        $response = $this->getJson('/api/categorias');

        $response->assertStatus(200);
    }

    /**
     * Test: ver categoria
     */
    public function test_se_puede_ver_una_categoria()
    {
        $categoria = Categoria::factory()->create();

        $response = $this->getJson('/api/categorias/' . $categoria->id);

        $response->assertStatus(200);
    }

    /**
     * Test: actualizar categoria
     */
    public function test_se_puede_actualizar_categoria()
    {
        $categoria = Categoria::factory()->create();

        $response = $this->putJson('/api/categorias/' . $categoria->id, [
            'nombre' => 'Sub-2000',
            'descripcion' => $categoria->descripcion,
            'edad_minima' => $categoria->edad_minima,
            'edad_maxima' => $categoria->edad_maxima,
            'estado' => $categoria->activo
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('categorias', [
            'nombre' => 'Sub-2000'
        ]);
    }

    /**
     * Test: eliminar categoria
     */
    public function test_se_puede_eliminar_categoria()
    {
        $categoria = Categoria::factory()->create();

        $response = $this->deleteJson('/api/categorias/' . $categoria->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('categorias', [
            'id' => $categoria->id
        ]);
    }
}
