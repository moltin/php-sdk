<?php

require_once('./init.php');

try {

    function outputLine($product) {
        $name = substr($product->name, 0, 10) . '...';
        echo "\t". $name . " (" . $product->id . ")\n";
    }

    if(count($moltin->products->all()->data()) > 10) {

        echo "Limit to 10:\n\n";
        foreach($moltin->products->limit(10)->all()->data() as $product) {
            outputLine($product);
        }

        echo "\nThen offset 10:\n\n";
        foreach($moltin->products->limit(10)->offset(10)->all()->data() as $product) {
            outputLine($product);
        }

        echo "\nFirst page (sorted by name this time):\n\n";
        foreach($moltin->products->limit(10)->sort('name')->all()->data() as $product) {
            outputLine($product);
        }

        echo "\nReverse the order:\n\n";
        foreach($moltin->products->limit(10)->sort('-name')->all()->data() as $product) {
            outputLine($product);
        }

    } else {

        echo "For this example to work (to demostrate limit and offset) you need more than 10 products. Why not write a SDK script here to do it then run it again? :)\n\n";

    }

} catch(Exception $e) {

    echo 'An exception occurred calling the moltin API:';
    var_dump($e);
    exit;

}
