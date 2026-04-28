<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rol;
use App\Models\Club;
use App\Models\Genero;
use App\Models\Usuario;
use App\Models\Categoria;
use App\Models\Deportista;
use App\Models\Nacionalidad;
use App\Models\TipoIdentificacion;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeportistaTest extends TestCase
{
    use RefreshDatabase;

    private $rol;
    private $tipoIdentificacion;
    private $usuario;
    private $genero;
    private $nacionalidad;
    private $club;
    private $categoria;

    protected function setUp(): void
    {
        parent::setUp();

        $rol = [
            'nombre' => 'Deportista',
            'descripcion' => 'Rol de un deportista'
        ];

        $this->rol = Rol::factory()->create($rol);
        $this->tipoIdentificacion = TipoIdentificacion::factory()->create();
        $this->genero = Genero::factory()->create();
        $this->nacionalidad = Nacionalidad::factory()->create();
        $this->club = Club::factory()->create();
        $this->categoria = Categoria::factory()->create();

        $this->usuario = Usuario::factory()
            ->withRol($this->rol->id)
            ->withTipoIdentificacion($this->tipoIdentificacion->id)
            ->create();

        // Permisos necesarios
        $this->asignarPermisoARol($this->rol, 'crear', 'deportistas');
        $this->asignarPermisoARol($this->rol, 'ver', 'deportistas');
        $this->asignarPermisoARol($this->rol, 'editar', 'deportistas');

        $this->actingAs($this->usuario, 'api');
    }

    /**
     * Test: crear deportista
     */
    public function test_se_puede_crear_deportista()
    {
        $usuario = Usuario::factory()
            ->withRol($this->rol->id)
            ->withTipoIdentificacion($this->tipoIdentificacion->id)
            ->create();

        $response = $this->postJson('/api/deportistas', [
            'usuario_id' => $usuario->id,
            'club_id' => $this->club->id,
            'fecha_nacimiento' => '1990-01-01',
            'genero_id' => $this->genero->id,
            'nacionalidad_id' => $this->nacionalidad->id,
            'elo_nacional' => 0,
            'fide_id' => '144413246',
            'elo_internacional' => null,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('deportistas', [
            'usuario_id' => $usuario->id
        ]);
    }

    /**
     * Test: listar deportistas
     */
    public function test_se_pueden_listar_deportistas()
    {
        $usuario = Usuario::factory()
            ->withRol($this->rol->id)
            ->withTipoIdentificacion($this->tipoIdentificacion->id)
            ->create();

        Deportista::factory()->create([
            'usuario_id' => $usuario->id
        ]);

        $response = $this->getJson('/api/deportistas');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'usuario']
                 ]);
    }

    /**
     * Test: validación
     */
    public function test_falla_creacion_deportista_si_faltan_datos()
    {
        $response = $this->postJson('/api/deportistas', []);

        $response->assertStatus(422);
    }

    /**
     * Test: ver detalles de un deportista
     */
    public function test_se_pueden_ver_detalles_de_un_deportista()
    {
        $usuario = Usuario::factory()
            ->withRol($this->rol->id)
            ->withTipoIdentificacion($this->tipoIdentificacion->id)
            ->create();

        $deportista = Deportista::factory()->create([
            'usuario_id' => $usuario->id
        ]);

        $response = $this->getJson('/api/deportistas/' . $deportista->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id', 'usuario'
                 ]);
    }
}
