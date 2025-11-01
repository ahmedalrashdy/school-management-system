<?php

use App\Models\Guardian;
use App\Models\Student;
use App\Models\Subject;

return [
    'default_per_page' => 10,
    'max_per_page' => 25,

    'resources' => [
        'students' => [
            'model' => Student::class,
            'text_column' => 'user.full_name',
            'searchable_columns' => [
                'user.first_name',
                'user.last_name',
                'admission_number',
            ],
            'order_by' => 'students.id',
            'order_direction' => 'desc',
        ],
        'subjects' => [
            'model' => Subject::class,
            'text_column' => 'name',
            'searchable_columns' => [
                'name',
            ],
            'order_by' => 'subjects.id',
            'order_direction' => 'asc',
        ],
        'guardians' => [
            'model' => Guardian::class,
            'text_column' => 'user.full_name',
            'searchable_columns' => [
                'user.first_name',
                'user.last_name',
                'user.phone_number',
            ],
            'order_by' => 'guardians.id',
            'order_direction' => 'desc',
        ],
        'teachers' => [
            'model' => \App\Models\Teacher::class,
            'text_column' => 'user.full_name',
            'searchable_columns' => [
                'user.first_name',
                'user.last_name',
                'user.phone_number',
                'user.email',
            ],
            'order_by' => 'teachers.id',
            'order_direction' => 'desc',
            // 'query_modifier' => function ($query) {
            //     $query->whereHas('user', function ($q) {
            //         $q->where('is_active', true);
            //     });
            // },
        ],
    ],
];

