<?php

require_once('./init.php');

try {

    session_start();

    function outputLine($product) {
        $name = substr($product->name, 0, 10) . '...';
        echo "\t". $name . " (" . $product->id . ")\n";
    }

    echo "Limit to 10:\n\n";
    foreach($moltin->products->limit(10)->get()->data() as $product) {
        outputLine($product);
    }

    echo "\nThen offset 10:\n\n";
    foreach($moltin->products->limit(10)->offset(10)->get()->data() as $product) {
        outputLine($product);
    }

    echo "\nFirst page (sorted by name this time):\n\n";
    foreach($moltin->products->limit(10)->sort('name')->get()->data() as $product) {
        outputLine($product);
    }

    echo "\nReverse the order:\n\n";
    foreach($moltin->products->limit(10)->sort('-name')->get()->data() as $product) {
        outputLine($product);
    }

} catch(Exception $e) {

    echo 'An exception occurred calling the moltin API:';
    var_dump($e);
    exit;

}
