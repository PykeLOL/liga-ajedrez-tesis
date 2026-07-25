<?php

namespace App\Http\Requests\Entrenamiento;

use App\Http\Requests\ApiFormRequest;

class UpdateEntrenamientoRequest extends ApiFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'club_id' => [
                'sometimes',
                'exists:clubes,id'
            ],

            'categoria_id' => [
                'sometimes',
                'exists:categorias,id'
            ],

            'genero_id' => [
                'sometimes',
                'exists:generos,id'
            ],

            'entrenador_id' => [
                'sometimes',
                'exists:entrenadores,id'
            ],

            'tipo_entrenamiento_id' => [
                'sometimes',
                'exists:tipos_entrenamiento,id'
            ],

            'evento_id' => [
                'nullable',
                'exists:eventos,id'
            ],

            'nombre' => [
                'sometimes',
                'string',
                'max:255'
            ],

            'descripcion' => [
                'nullable',
                'string'
            ],

            'observaciones' => [
                'nullable',
                'string'
            ],

            'fecha' => [
                'required',
                'date'
            ],

            'hora_inicio' => [
                'required',
                'date_format:H:i'
            ],

            'hora_fin' => [
                'required',
                'date_format:H:i',
                'after:hora_inicio'
            ],

            'ubicacion' => [
                'nullable',
                'string',
                'max:255'
            ],

            'url_mapa' => [
                'nullable',
                'url',
                'max:500'
            ],

            'deportistas' => [
                'nullable',
                'array'
            ],

            'deportistas.*' => [
                'exists:deportistas,id'
            ],
        ];
    }

    public function messages()
    {
        return [
            'hora_fin.after' => 'La hora de finalización debe ser mayor que la hora de inicio.',
            'url_mapa.url' => 'La URL del mapa no es válida.',
        ];
    }
}
