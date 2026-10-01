<?php

return [
    'array' => 'El campo :attribute debe ser una lista.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'email' => 'El campo :attribute debe ser un correo válido.',
    'exists' => 'El :attribute seleccionado no existe.',
    'file' => 'El campo :attribute debe ser un archivo.',
    'in' => 'El :attribute seleccionado no es válido.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'max' => [
        'array' => 'El campo :attribute no debe tener más de :max elementos.',
        'file' => 'El archivo :attribute no debe superar los :max kilobytes.',
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'string' => 'El campo :attribute no debe tener más de :max caracteres.',
    ],
    'min' => [
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El archivo :attribute debe pesar al menos :min kilobytes.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'required' => 'El campo :attribute es obligatorio.',
    'unique' => 'El :attribute ya está registrado.',
    'uploaded' => 'No se pudo subir :attribute (revisa el tamaño máximo permitido).',

    'custom' => [],

    'attributes' => [
        'nombre' => 'nombre',
        'correo' => 'correo',
        'password' => 'contraseña',
        'archivo' => 'archivo',
        'archivos' => 'archivos',
        'permiso' => 'permiso',
    ],
];
