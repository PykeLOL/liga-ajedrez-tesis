<?php

namespace App\Http\Requests\Entrenamiento;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiFormRequest;

class StoreEntrenamientoRequest extends ApiFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'plan_entrenamiento_id' => [
                'nullable',
                'exists:planes_entrenamiento,id'
            ],

            'club_id' => [
                Rule::requiredIf(!$this->filled('plan_entrenamiento_id')),
                'exists:clubes,id'
            ],

            'categoria_id' => [
                Rule::requiredIf(!$this->filled('plan_entrenamiento_id')),
                'exists:categorias,id'
            ],

            'genero_id' => [
                Rule::requiredIf(!$this->filled('plan_entrenamiento_id')),
                'exists:generos,id'
            ],

            'entrenador_id' => [
                Rule::requiredIf(!$this->filled('plan_entrenamiento_id')),
                'exists:entrenadores,id'
            ],

            'tipo_entrenamiento_id' => [
                Rule::requiredIf(!$this->filled('plan_entrenamiento_id')),
                'exists:tipos_entrenamiento,id'
            ],

            'evento_id' => [
                'nullable',
                'exists:eventos,id'
            ],

            'nombre' => [
                Rule::requiredIf(!$this->filled('plan_entrenamiento_id')),
                'nullable',
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
            'club_id.required' => 'Debe seleccionar un club.',
            'categoria_id.required' => 'Debe seleccionar una categoría.',
            'genero_id.required' => 'Debe seleccionar un género.',
            'entrenador_id.required' => 'Debe seleccionar un entrenador.',
            'tipo_entrenamiento_id.required' => 'Debe seleccionar un tipo de entrenamiento.',
            'hora_fin.after' => 'La hora de finalización debe ser mayor que la hora de inicio.',
            'url_mapa.url' => 'La URL del mapa no es válida.',
        ];
    }
}
