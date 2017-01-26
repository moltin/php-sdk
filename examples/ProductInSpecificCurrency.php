<?php

require_once('./init.php');

try {

    // create two new currencies
    $currencyData = [
        [
            'code' => 'ABC',
            'exchange_rate' => 1,
            'default' => false,
            'enabled' => true,
            'decimal_point' => '.',
            'decimal_places' => 2,
            'thousand_separator' => ',',
            'format' => '$-ABC {price}'
        ],
        [
            'code' => 'DEF',
            'exchange_rate' => 1,
            'default' => false,
            'enabled' => true,
            'decimal_point' => '.',
            'decimal_places' => 2,
            'thousand_separator' => ',',
            'format' => '$-DEF {price}'
        ]
    ];
    $currencies = [];

    foreach($currencyData as $currency) {
        $createCurrency = $moltin->currencies->create($currency);
        if ($createCurrency->getStatusCode() !== 201) {
            throw new Exception('Couldn\'t create currency');
        }
        $currencies[] = $createCurrency->data();
    }


    // create a product with both currency prices
    $unique = sha1(rand(0,5000) . microtime());
    $productCreateResponse = $moltin->products->create([
        'type' => 'product',
        'name' => 'My Product ' . $unique,
        'slug' => 'my-product ' . $unique,
        'sku' => 'my.prod.' . $unique,
        'description' => 'a bit about it',
        'manage_stock' => false,
        'status' => 'live',
        'commodity_type' => 'digital',
        'price' => [
            [
                'amount' => 5000,
                'currency' => $currencies[0]->code,
                'includes_tax' => true
            ],
            [
                'amount' => 3500,
                'currency' => $currencies[1]->code,
                'includes_tax' => true
            ]
        ]
    ]);
    if ($productCreateResponse->getStatusCode() === 201) {

        $product = $productCreateResponse->data();
        echo "Product created (" . $productCreateResponse->getExecutionTime() . " secs)\n";

        // get the original product and view the `body.data.price` array:
        echo "\n\nProduct Price: " . print_r($product->price, true) . "\n\n";

        echo "Request in ABC currency:\n\n";
        $productGetResponse = $moltin->currency('ABC')->products->get($product->id);
        echo "meta.display_price: ".  print_r($productGetResponse->data()->meta->display_price, true);

        echo "Request in DEF currency:\n\n";
        $productGetResponse = $moltin->currency('DEF')->products->get($product->id);
        echo "meta.display_price: ".  print_r($productGetResponse->data()->meta->display_price, true);

        // delete the currencies
        foreach($currencies as $currency) {
            $deleteCurrencyResponse = $moltin->currencies->delete($currency->id);
            if ($deleteCurrencyResponse->getStatusCode() === 200) {
                echo "[Currency removed (" . $deleteCurrencyResponse->getExecutionTime() . " secs)]\n";
            }
        }

        // delete the product
        $deleteProductResponse = $moltin->currency(false)->products->delete($product->id);
        if ($deleteProductResponse->getStatusCode() === 200) {
            echo "[Product removed (" . $deleteProductResponse->getExecutionTime() . " secs)]\n";
        }
    }

} catch(Exception $e) {

    echo 'An exception occurred calling the moltin API:';
    var_dump($e);
    exit;

}
