<?php

require_once('./init.php');

try {

    $response = $moltin->currency(false)->products->filter(['eq' => ['status' => 'live', 'commodity_type' => 'digital']])->all();

    $products = $response->data();
    echo "\n\tExecution time:\t\t" . $response->getExecutionTime() . " seconds (inc network)";
    echo "\n\tMoltin Trace ID:\t" . $response->getRequestID() . "\n\n";

    if (!empty($products)) {

        $format = 'table';
        if (isset($argv[1])) {
            if (explode("=", $argv[1])[1] === 'json') {
                $format = 'json';
            }
        }

        if ($format === 'table') {

            $table = new Console_Table();
            $table->setHeaders(['ID / Name', 'Status', 'Type', 'Stock', 'SKU']);

            $i = 0;
            foreach($products as $product) {
                $table->addRow([
                    $product->id . "\n" . $product->name,
                    $product->status,
                    $product->commodity_type,
                    $product->meta->stock->level,
                    $product->sku,
                ]);
                if ($i < count($products) - 1) {
                    $table->addSeparator();
                }
                $i++;
            }

            echo $table->getTable();

        } else if ($format === 'json') {

            print_r($response->getRaw());

        }

    } else {
        echo "Please add some products to your store to test filtering...";
    }

} catch(Exception $e) {

    echo 'An exception occurred calling the moltin API:';
    var_dump($e);
    exit;

}
