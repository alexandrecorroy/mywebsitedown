<?php

namespace App\Helper;

final class FunctionsHelper
{

    public function generateTokenForUser($email): string
    {
        $data = uniqid(rand(), true) . $email . time();
        return hash('sha256', $data);
    }

}