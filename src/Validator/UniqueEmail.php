<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class UniqueEmail extends Constraint
{
    public string $message = 'The email "{{ value }}" is already in use.';
}
