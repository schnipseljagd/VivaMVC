<?php
if (!function_exists('vmvc_autoload')) {
    function vmvc_autoload($class)
    {
        if (strpos($class, 'Vmvc_') === 0) {
            $file = str_replace('_', '/', $class) . '.php';
            if ($file) {
                require $file;
            }
        }
    }

    spl_autoload_register('vmvc_autoload');
}