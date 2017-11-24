# phpArray2Table
PHP library with zero dependencies that convert array data into ascii table.

Based on https://github.com/viossat/arraytotexttable.

## Features
* Zero dependencies (custom Decorator class).
* Numeric values are automatic aligned to right
* Optional limit for values (adding ellipsis `…`)

## Usage
```php
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
```

prints:
```
┌───────────┬─────────┬───────────────────────────┬─────┐
│ FIRSTNAME │ SURNAME │           EMAIL           │ AGE │
├───────────┼─────────┼───────────────────────────┼─────┤
│ Mollie    │ Alvarez │ molliealvarez@example.com │     │
│ Dianna    │ Mcbride │ diannamcbride@example.com │  43 │
│ Elvira    │ Mueller │ elviramueller@example.com │   5 │
│ Corine    │ Morton  │                           │  35 │
│ James     │ Allison │                           │     │
│ Bowen     │ Kelley  │ bowenkelley@example.com   │  50 │
└───────────┴─────────┴───────────────────────────┴─────┘
```

## Options
### setValueMaxLength
```php
$renderer->setValueMaxLength(10);
```
prints:
```
┌───────────┬─────────┬─────────────┬─────┐
│ FIRSTNAME │ SURNAME │    EMAIL    │ AGE │
├───────────┼─────────┼─────────────┼─────┤
│ Mollie    │ Alvarez │ molliealva… │     │
│ Dianna    │ Mcbride │ diannamcbr… │  43 │
│ Elvira    │ Mueller │ elviramuel… │   5 │
│ Corine    │ Morton  │             │  35 │
│ James     │ Allison │             │     │
│ Bowen     │ Kelley  │ bowenkelle… │  50 │
└───────────┴─────────┴─────────────┴─────┘
```
