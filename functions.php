<?php
// If called directly
if (!function_exists('add_action')) {
    echo 'You think darkness is your ally :)';
    exit;
}

// Autoload 
require __DIR__ . '/vendor/autoload.php';

// Application config file
require __DIR__ . '/src/config.php';
config(config: 'r');
