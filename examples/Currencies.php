<?php

require_once('./init.php');

try {

    $response = $moltin->currencies->all();
    $currencies = $response->data();
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
        $table->setHeaders(['Code / ID', 'Rate', 'Format', 'Decimal Point', 'Thousands Separator', 'Default', 'Enabled']);

        $i = 0;
        foreach($currencies as $currency) {
            $table->addRow([
                $currency->code . "\n(" . $currency->id . ")",
                $currency->exchange_rate,
                $currency->format,
                $currency->decimal_point,
                $currency->thousand_separator,
                $currency->default ? 'true' : 'false',
                $currency->enabled ? 'true' : 'false',
            ]);
            if ($i < count($currencies) - 1) {
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
