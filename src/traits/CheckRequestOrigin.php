<?php

namespace Inc\traits;


trait CheckRequestOrigin
{
    public function originValidate()
    {
        $allowed = config('app_mode') === 'prod' ? [''] : ['http://127.0.0.1:3000', 'http://localhost:3000'];

        if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed))
            return true;
        else
            return false;

    }
}