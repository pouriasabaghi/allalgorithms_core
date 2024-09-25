<?php 

/**
 * get app config
 * @param string $config config that you want
 * @return string
 */
function config(string $config): string{
    $configs = [
        'api_base_url'=>'',
        'app_mode'=>'local' // prod, dev, local
    ];

    return $configs[$config] ;
}