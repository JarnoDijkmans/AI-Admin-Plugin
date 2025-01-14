<?php
    if( !defined('ABSPATH'))
    {
        exit;
    }

    $includes = array(
        'enqueue-admin-scripts'
    );

    foreach($includes as $include) :
        $file = plugin_dir_path(__FILE__) . 'php/' .$include.'.php';
        include_once $file;
    endforeach;
?>