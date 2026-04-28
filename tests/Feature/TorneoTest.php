<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rol;
use App\Models\Liga;
use App\Models\Evento;
use App\Models\Genero;
use App\Models\Usuario;
use App\Models\Categoria;
use App\Models\TipoEvento;
use App\Models\EstadoEvento;
use App\Models\TipoIdentificacion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TorneoTest extends TestCase
{
    use RefreshDatabase;

    private $rol;
    private $usuario;
    private $tipoIdentificacion;
    private $liga;
    private $tipoEvento;
    private $categoria;
    private $genero;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->rol = Rol::factory()->create();
        $this->tipoIdentificacion = TipoIdentificacion::factory()->create();
        $this->categoria = Categoria::factory()->create();
        $this->genero = Genero::factory()->create();

        $this->usuario = Usuario::factory()
            ->withRol($this->rol->id)
            ->withTipoIdentificacion($this->tipoIdentificacion->id)
            ->create();

        // Permisos
        $this->asignarPermisoARol($this->rol, 'crear', 'eventos');
        $this->asignarPermisoARol($this->rol, 'ver', 'eventos');

        $this->actingAs($this->usuario, 'api');

        // Dependencias
        $this->liga = Liga::factory()->create();

        $this->tipoEvento = TipoEvento::factory()->create([
            'nombre' => 'Torneo'
        ]);

        EstadoEvento::factory()->create(['nombre' => 'Borrador']);
        EstadoEvento::factory()->create(['nombre' => 'Publicado']);
        EstadoEvento::factory()->create(['nombre' => 'En Curso']);
    }

    /**
     * Test: crear torneo correctamente
     */
    public function test_se_puede_crear_torneo()
    {
        $response = $this->postJson('/api/torneos', [
            'liga_id' => $this->liga->id,
            'tipo_evento_id' => $this->tipoEvento->id,
            'nombre' => 'Torneo Test',
            'descripcion' => 'Descripción test',
            'lugar' => 'Villavicencio',
            'direccion' => 'Calle 123',
            'fecha_inicio' => now()->addDay()->format('Y-m-d'),
            'fecha_fin' => now()->addDays(2)->format('Y-m-d'),
            'de_pago' => false,
            'publicado' => false,
            'categorias' => [
                [
                    'categoria_id' => $this->categoria->id,
                    'genero_id' => $this->genero->id,
                ]
            ],
            'media' => [
                [
                    'tipo' => 'imagen',
                    'archivo' => UploadedFile::fake()->image('test.jpg'),
                    'orden' => 1,
                ]
            ]
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Torneo creado exitosamente'
                 ]);

        $this->assertDatabaseHas('eventos', [
            'nombre' => 'Torneo Test'
        ]);
    }

    /**
     * Test: validación
     */
    public function test_falla_creacion_torneo_si_faltan_datos()
    {
        $response = $this->postJson('/api/torneos', []);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'errors'
                 ]);
    }

    /**
     * Test: listar torneos
     */
    public function test_se_pueden_listar_torneos()
    {
        Evento::factory()->create([
            'liga_id' => $this->liga->id,
            'tipo_evento_id' => $this->tipoEvento->id,
        ]);

        $response = $this->getJson('/api/torneos');

        $response->assertStatus(200);
    }
}
