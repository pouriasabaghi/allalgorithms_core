<?php

namespace Src\traits;


trait CheckRequestOrigin
{
    public function isOriginValid()
    {
        if (config('app_mode') === 'prod') {
            $allowed = config('allowed_prod_origin')[0] === "*"
                ? true
                : config('allowed_prod_origin');
        } else {
            $allowed = config('allowed_local_origin')[0] === "*"
                ? true
                : config('allowed_local_origin');
        }

        if ($allowed === true)
            return $allowed;


        if (is_array($allowed))
            if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed))
                return true;
            else
                return false;

        return false;

    }
}