<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Rol;
use App\Models\Usuario;
use App\Models\TipoIdentificacion;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private $rol;
    private $tipoIdentificacion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rol = Rol::factory()->create();
        $this->tipoIdentificacion = TipoIdentificacion::factory()->create();
    }

    /**
     * Test: usuario puede iniciar sesión correctamente
     */
    public function test_usuario_puede_loguearse_correctamente()
    {
        Usuario::factory()
            ->withRol($this->rol->id)
            ->withTipoIdentificacion($this->tipoIdentificacion->id)
            ->withPassword('12345678')
            ->create([
                'email' => 'test@test.com',
            ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@test.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Inicio de sesión exitoso',
                ])
                ->assertJsonStructure([
                    'user' => ['id', 'nombre', 'email'],
                    'permisos'
                ]);
    }

    /**
     * Test: login falla con credenciales incorrectas
     */
    public function test_login_falla_con_credenciales_incorrectas()
    {
        Usuario::factory()
            ->withRol($this->rol->id)
            ->withTipoIdentificacion($this->tipoIdentificacion->id)
            ->withPassword('12345678')
            ->create([
                'email' => 'test@test.com',
            ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);

        $this->assertGuest();
    }

    /**
     * Test: login falla si faltan campos
     */
    public function test_login_falla_si_faltan_campos()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email', 'password']);
    }
}
