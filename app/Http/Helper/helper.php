<?php

function da($data){
    if (is_object($data) && method_exists($data, 'toArray')) {
        $data = $data->toArray();
    }
    echo "<pre>";
    print_r($data, false);
    echo "</pre>";
    exit;
}
