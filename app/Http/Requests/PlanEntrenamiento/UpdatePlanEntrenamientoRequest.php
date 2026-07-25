<?php

namespace App\Http\Requests\PlanEntrenamiento;

use App\Http\Requests\ApiFormRequest;

class UpdatePlanEntrenamientoRequest extends ApiFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'club_id' => 'required|exists:clubes,id',
            'categoria_id' => 'required|exists:categorias,id',
            'genero_id' => 'required|exists:generos,id',
            'entrenador_id' => 'required|exists:entrenadores,id',

            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',

            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',

            'evento_id' => 'nullable|exists:eventos,id',

            'horarios' => 'required|array|min:1',

            'horarios.*.dia_semana_id' => 'required|exists:dias_semana,id',
            'horarios.*.hora_inicio' => 'required|date_format:H:i',
            'horarios.*.hora_fin' => 'required|date_format:H:i',

            'deportistas' => 'nullable|array',

            'deportistas.*' => 'exists:deportistas,id',

            'tipo_entrenamiento_id' => 'required|exists:tipos_entrenamiento,id',

            'ubicacion' => 'nullable|string|max:255',

            'url_mapa' => 'nullable|url|max:500',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($this->horarios ?? [] as $index => $horario) {
                if (
                    isset($horario['hora_inicio']) &&
                    isset($horario['hora_fin']) &&
                    $horario['hora_fin'] <= $horario['hora_inicio']
                ) {
                    $validator->errors()->add(
                        "horarios.$index.hora_fin",
                        'La hora de fin debe ser mayor que la hora de inicio.'
                    );
                }
            }
        });
    }

    public function attributes()
    {
        return [
            'club_id' => 'club',
            'categoria_id' => 'categoría',
            'genero_id' => 'género',
            'entrenador_id' => 'entrenador',
            'evento_id' => 'evento',
            'dia_semana_id' => 'día de la semana',
        ];
    }
}
