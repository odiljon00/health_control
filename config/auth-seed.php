<?php

return [
    'admin' => [
        'name'     => env('ADMIN_NAME', 'Administrator'),
        'email'    => env('ADMIN_EMAIL', 'admin@soglomavlod.uz'),
        'password' => env('ADMIN_PASSWORD', 'password'),
        'role'     => 'admin',
    ],

    'teacher' => [
        'name'     => env('TEACHER_NAME', 'Nodira Karimova'),
        'email'    => env('TEACHER_EMAIL', 'teacher@soglomavlod.uz'),
        'password' => env('TEACHER_PASSWORD', 'password'),
        'role'     => 'teacher',
        'phone'    => env('TEACHER_PHONE', '+998901112233'),
    ],

    'psychologist' => [
        'name'     => env('PSYCHOLOGIST_NAME', 'Dilshod Rahimov'),
        'email'    => env('PSYCHOLOGIST_EMAIL', 'psychologist@soglomavlod.uz'),
        'password' => env('PSYCHOLOGIST_PASSWORD', 'password'),
        'role'     => 'psychologist',
        'phone'    => env('PSYCHOLOGIST_PHONE', '+998902223344'),
    ],

    'parent' => [
        'name'     => env('PARENT_NAME', 'Zilola Yusupova'),
        'email'    => env('PARENT_EMAIL', 'parent@soglomavlod.uz'),
        'password' => env('PARENT_PASSWORD', 'password'),
        'role'     => 'parent',
        'phone'    => env('PARENT_PHONE', '+998903334455'),
    ],

    'iio' => [
        'name'     => env('IIO_NAME', 'Kapitan Bobur Aliyev'),
        'email'    => env('IIO_EMAIL', 'iio@soglomavlod.uz'),
        'password' => env('IIO_PASSWORD', 'password'),
        'role'     => 'iio',
        'phone'    => env('IIO_PHONE', '+998993554567'),
    ],
];