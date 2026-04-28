<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Liga;
use App\Models\TipoEvento;
use App\Models\EstadoEvento;

class EventoFactory extends Factory
{
    public function definition()
    {
        return [
            'liga_id' => Liga::factory(),

            'nombre' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraph(),

            'tipo_evento_id' => TipoEvento::factory()->state([
                'nombre' => 'Festival' // importante: NO "Torneo"
            ]),

            'lugar' => $this->faker->city(),
            'direccion' => $this->faker->address(),
            'url_mapa' => null,

            'fecha_inicio' => now()->addDays(2),
            'hora_inicio' => '08:00:00',
            'fecha_fin' => now()->addDays(3),

            'estado_evento_id' => EstadoEvento::factory()->state([
                'nombre' => 'Borrador'
            ]),

            'de_pago' => false,
            'valor_rango' => null,

            'organizador_nombre' => $this->faker->name(),
            'organizador_contacto' => $this->faker->phoneNumber(),

            'publicado' => false,
            'es_oficial' => false,

            'max_participantes' => $this->faker->numberBetween(10, 100),
        ];
    }

    /**
     * Usar liga existente
     */
    public function withLiga($ligaId)
    {
        return $this->state(fn () => [
            'liga_id' => $ligaId,
        ]);
    }

    /**
     * Usar tipo evento existente
     */
    public function withTipoEvento($tipoEventoId)
    {
        return $this->state(fn () => [
            'tipo_evento_id' => $tipoEventoId,
        ]);
    }

    /**
     * Usar estado existente
     */
    public function withEstado($estadoId)
    {
        return $this->state(fn () => [
            'estado_evento_id' => $estadoId,
        ]);
    }
}
