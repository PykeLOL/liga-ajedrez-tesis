<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rol;
use App\Models\Club;
use App\Models\Liga;
use App\Models\Estado;
use App\Models\Usuario;
use Illuminate\Http\UploadedFile;
use App\Models\TipoIdentificacion;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClubTest extends TestCase
{
    use RefreshDatabase;

    private $rol;
    private $usuario;
    private $tipoIdentificacion;
    private $liga;
    private $estado;

    protected function setUp(): void
    {
        parent::setUp();

        // Base
        $this->rol = Rol::factory()->create();
        $this->tipoIdentificacion = TipoIdentificacion::factory()->create();
        $this->liga = Liga::factory()->create();
        $this->estado = Estado::factory()->create();

        $this->usuario = Usuario::factory()
            ->withRol($this->rol->id)
            ->withTipoIdentificacion($this->tipoIdentificacion->id)
            ->create();

        // Permisos
        $this->asignarPermisoARol($this->rol, 'crear', 'clubes');
        $this->asignarPermisoARol($this->rol, 'ver', 'clubes');
        $this->asignarPermisoARol($this->rol, 'editar', 'clubes');
        $this->asignarPermisoARol($this->rol, 'eliminar', 'clubes');

        $this->actingAs($this->usuario, 'api');
    }

    /**
     * Test: crear club
     */
    public function test_se_puede_crear_club()
    {
        $response = $this->postJson('/api/clubes', [
            'liga_id' => $this->liga->id,
            'nombre' => 'Club Ajedrez Meta',
            'descripcion' => 'Club de prueba',
            'ubicacion' => 'Villavicencio',
            'direccion' => 'Calle 123',
            'url_mapa' => 'https://maps.google.com',
            'presidente_id' => $this->usuario->id,
            'contacto' => 'club@test.com',
            'logo' => UploadedFile::fake()->image('test.jpg'),
            'estado_id' => $this->estado->id,
            'media' => [
                [
                    'tipo' => 'url',
                    'url' => 'https://www.youtube.com/watch?v=qR58X4Yrzb8',
                    'orden' => 1,
                ]
            ],
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('clubes', [
            'nombre' => 'Club Ajedrez Meta'
        ]);
    }

    /**
     * Test: listar clubes
     */
    public function test_se_pueden_listar_clubes()
    {
        Club::factory()
            ->withLiga($this->liga->id)
            ->withPresidente($this->usuario->id)
            ->create();

        $response = $this->getJson('/api/clubes');

        $response->assertStatus(200);
    }

    /**
     * Test: ver un club
     */
    public function test_se_puede_ver_un_club()
    {
        $club = Club::factory()
            ->withLiga($this->liga->id)
            ->withPresidente($this->usuario->id)
            ->create();

        $response = $this->getJson('/api/clubes/' . $club->id);

        $response->assertStatus(200);
    }

    /**
     * Test: actualizar club
     */
    public function test_se_puede_actualizar_club()
    {
        $club = Club::factory()
            ->withLiga($this->liga->id)
            ->withPresidente($this->usuario->id)
            ->create();

        $response = $this->putJson('/api/clubes/' . $club->id, [
            'nombre' => 'Club Actualizado',
            'liga_id' => $this->liga->id,
            'descripcion' => $club->descripcion,
            'ubicacion' => $club->ubicacion,
            'direccion' => $club->direccion,
            'url_mapa' => $club->url_mapa,
            'presidente_id' => $club->presidente_id,
            'contacto' => $club->contacto,
            'estado_id' => $club->estado_id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('clubes', [
            'nombre' => 'Club Actualizado'
        ]);
    }

    /**
     * Test: eliminar club
     */
    public function test_se_puede_eliminar_club()
    {
        $club = Club::factory()
            ->withLiga($this->liga->id)
            ->withPresidente($this->usuario->id)
            ->create();

        $response = $this->deleteJson('/api/clubes/' . $club->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('clubes', [
            'id' => $club->id
        ]);
    }
}
