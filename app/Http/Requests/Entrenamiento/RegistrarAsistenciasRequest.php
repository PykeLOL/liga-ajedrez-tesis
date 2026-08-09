<?php

namespace App\Http\Requests\Entrenamiento;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarAsistenciasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deportistas' => ['required', 'array', 'min:1'],

            'deportistas.*.id' => [
                'required',
                'integer',
                'exists:deportistas,id',
            ],

            'deportistas.*.estado_asistencia_id' => [
                'required',
                'integer',
                'exists:estados_asistencia,id',
            ],

            'deportistas.*.hora_llegada' => [
                'nullable',
                'date_format:H:i',
            ],

            'deportistas.*.observaciones' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'deportistas.required' => 'Debe enviar las asistencias de los deportistas.',
            'deportistas.array' => 'El formato de las asistencias es inválido.',
            'deportistas.min' => 'Debe registrar al menos un deportista.',

            'deportistas.*.id.exists' => 'Uno de los deportistas no existe.',
            'deportistas.*.estado_asistencia_id.exists' => 'Uno de los estados de asistencia no existe.',
            'deportistas.*.hora_llegada.date_format' => 'La hora de llegada debe tener el formato HH:MM.',
            'deportistas.*.observaciones.max' => 'Las observaciones no pueden superar los 500 caracteres.',
        ];
    }
}
