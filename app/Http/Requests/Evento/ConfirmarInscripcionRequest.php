<?php

namespace App\Http\Requests\Evento;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmarInscripcionRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'evento_categoria_id' => [
                'required',
                'integer',
                'exists:evento_categorias,id'
            ],
            'comprobante' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120'
            ]
        ];
    }

    public function messages()
    {
        return [
            'evento_categoria_id.required' => 'Debe seleccionar una categoría.',
            'evento_categoria_id.exists' => 'La categoría seleccionada no existe.',
            'comprobante.mimes' => 'El comprobante debe ser PDF o una imagen.',
            'comprobante.max' => 'El comprobante no puede superar los 5 MB.'
        ];
    }
}
