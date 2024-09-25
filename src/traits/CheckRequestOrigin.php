<?php

namespace Src\traits;


trait CheckRequestOrigin
{
    public function isOriginValid()
    {
        $allowedOrigins = config(config('app_mode') === 'prod' ? 'allowed_prod_origin' : 'allowed_local_origin');
    
        if ($allowedOrigins[0] === "*") 
            return true;

        if (is_array($allowedOrigins) && isset($_SERVER['HTTP_ORIGIN'])) 
            return in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins);
        
        return false;
    }
    
}