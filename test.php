<?php

require 'phpArray2Table.php';

$data = [
    [
        'firstname' => 'Mollie',
        'surname' => 'Alvarez',
        'email' => 'molliealvarez@example.com',
    ],
    [
        'firstname' => 'Dianna',
        'surname' => 'Mcbride',
        'age' => 43,
        'email' => 'diannamcbride@example.com',
    ],
    [
        'firstname' => 'Elvira',
        'surname' => 'Mueller',
        'age' => 5,
        'email' => 'elviramueller@example.com',
    ],
    [
        'firstname' => 'Corine',
        'surname' => 'Morton',
        'age' => 35,
    ],
    [
        'firstname' => 'James',
        'surname' => 'Allison',
    ],
    [
        'firstname' => 'Bowen',
        'surname' => 'Kelley',
        'age' => 50,
        'email' => 'bowenkelley@example.com',
    ]
];

$renderer = new phpArray2Table($data);
echo $renderer->getTable();
