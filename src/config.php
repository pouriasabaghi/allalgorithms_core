<?php

// setup theme
add_action('after_setup_theme', function () {
    add_theme_support('post-thumbnails');
});

// add classic editor 
add_filter('use_block_editor_for_post', '__return_false');


/**
 * get app config
 * @param string $config config that you want
 * @return string
 */
function config(string $config): mixed
{
    $configs = [
        'api_base_url' => '',
        'app_mode' => 'local', // prod, dev, local
        'allowed_local_origin' => ['*'],
        'allowed_prod_origin' => ['*']
    ];

    return $configs[$config];
}