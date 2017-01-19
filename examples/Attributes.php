<?php

require_once('./init.php');

try {

    $response = $moltin->products->attributes();
    $attributes = $response->data();
    echo "\n\tExecution time:\t\t" . $response->getExecutionTime() . " seconds (inc network)";
    echo "\n\tMoltin Trace ID:\t" . $response->getRequestID() . "\n\n";

    $format = 'table';
    if (isset($argv[1])) {
        if (explode("=", $argv[1])[1] === 'json') {
            $format = 'json';
        }
    }

    if ($format === 'table') {

        echo "\tEntity:\t\t\t" . $response->meta()->entity . "\n\tVersion:\t\t" . $response->meta()->version . "\n\n";

        $table = new Console_Table();
        $table->setHeaders(['Label (Value)', 'Type', 'Required', 'Default', 'Options', 'Misc']);

        $i = 0;
        foreach($attributes as $attribute) {
            $table->addRow([
                $attribute->label . "\n(" . $attribute->value . ")",
                $attribute->type,
                $attribute->required ? 'true' : 'false',
                isset($attribute->default) ? $attribute->default : '',
                isset($attribute->options) ? implode(",\n", $attribute->options) : '',
                isset($attribute->object) ? print_r($attribute->object, true) : (isset($attribute->items) ? print_r($attribute->items, true) : '')
            ]);
            if ($i < count($attributes) - 1) {
                $table->addSeparator();
            }
            $i++;
        }

        echo $table->getTable();

    } else if ($format === 'json') {

        print_r($response->getRaw());

    }


} catch(Exception $e) {

    echo 'An exception occurred calling the moltin API:';
    var_dump($e);
    exit;

}
