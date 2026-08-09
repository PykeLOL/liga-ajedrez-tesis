<?php

namespace App\Http\Requests\Torneo;

use Illuminate\Validation\Rule;
use App\Models\EstadoInscripcion;
use Illuminate\Foundation\Http\FormRequest;

class ActualizarInscripcionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'estado_inscripcion_id' => [
                'required',
                Rule::in([
                    EstadoInscripcion::PENDIENTE,
                    EstadoInscripcion::PAGADO,
                    EstadoInscripcion::RECHAZADO,
                    EstadoInscripcion::CANCELADO,
                ]),
            ],

            'valor_pagado' => [
                'nullable',
                'required_if:estado_inscripcion_id,' . EstadoInscripcion::PAGADO,
                'numeric',
                'min:0'
            ],

            'referencia_pago' => [
                'nullable',
                'string',
                'max:255'
            ],

            'observacion' => [
                'nullable',
                'string',
                'max:1000'
            ],
        ];
    }

    public function messages()
    {
        return [
            'estado_inscripcion_id.required' => 'Debe seleccionar un estado.',
            'estado_inscripcion_id.in' => 'El estado seleccionado no es válido.',

            'valor_pagado.numeric' => 'El valor pagado debe ser numérico.',
            'valor_pagado.min' => 'El valor pagado no puede ser negativo.',
            'valor_pagado.required_if' => 'El valor pagado es obligatorio si el estado es Pagado',

            'referencia_pago.max' => 'La referencia de pago no puede superar los 255 caracteres.',

            'observacion.max' => 'La observación no puede superar los 1000 caracteres.',
        ];
    }
}
