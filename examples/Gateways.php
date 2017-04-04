<?php

require_once('./init.php');

try {

    $response = $moltin->gateways->all();
    $gateways = $response->data();
    echo "\n\tExecution time:\t\t" . $response->getExecutionTime() . " seconds (inc network)";
    echo "\n\tMoltin Trace ID:\t" . $response->getRequestID() . "\n\n";


    $format = 'table';
    if (isset($argv[1])) {
        if (explode("=", $argv[1])[1] === 'json') {
            $format = 'json';
        }
    }

    if ($format === 'table') {

        $table = new Console_Table();
        $table->setHeaders(['Name / Slug', 'Type', 'Enabled', 'Congig']);

        $i = 0;
        foreach($gateways as $gateway) {
            $config = clone $gateway;
            unset($config->slug);
            unset($config->name);
            unset($config->type);
            unset($config->enabled);

            $table->addRow([
                $gateway->name . " / " . $gateway->slug,
                $gateway->type,
                $gateway->enabled ? 'true' : 'false',
                print_r($config, true)
            ]);
            if ($i < count($gateways) - 1) {
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
