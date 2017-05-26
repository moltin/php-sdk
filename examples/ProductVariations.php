<?php

require_once('./init.php');

try {

    // this example follows the example documented here:
    // https://moltin.api-docs.io/v2/product-variations/variations-example

    // Create the base product
    $base = $moltin->products->create([
        "type" => "product",
        "name" => "iPad Mini 4",
        "slug" => "ipad-mini-4",
        "sku" => "mini-4",
        "manage_stock" => true,
        "status" => "live",
        "commodity_type" => "physical",
        "description" => "Learn, play, surf, create. iPad gives you the incredible display, performance and apps to do what you love to do. Anywhere. Easily. Magically.",
        "price" => [
            [
                "currency" => "GBP",
                "includes_tax" => true,
                "amount" => "41900"
            ]
        ],
        "stock" => 1000
    ])->data();


    // all variations in a variable we can iterate
    $variations = [
        "Colour" => [
            "silver" => [
                "name" => "Silver",
                "name_append" => " Silver",
                "sku_append" => ".SLVR",
                "slug_append" => "-silver",
                "description" => "Silver iPad"
            ],
            "gold" => [
                "name" => "Gold",
                "name_append" => " Gold",
                "sku_append" => ".GLD",
                "slug_append" => "-gold",
                "description" => "Gold iPad"
            ],
            "grey" => [
                "name" => "Space Grey",
                "name_append" => " Space Grey",
                "sku_append" => ".SPGCRY",
                "slug_append" => "-space-grey",
                "description" => "Space Grey iPad"
            ]
        ],
        "Storage" => [
            "128gb" => [
                "name" => "128GB",
                "name_append" => " 128GB",
                "sku_append" => ".128GB",
                "slug_append" => "-128",
                "description" => "128GB Storage"
            ]
        ],
        "Connectivity" => [
            "wifi" => [
                "name" => "Wifi",
                "name_append" => " Wifi",
                "sku_append" => ".WIFI",
                "slug_append" => "-wifi",
                "description" => "Wifi only connectivity"
            ],
            "wifiAndCellular" => [
                "name" => "Wifi",
                "name_append" => " Wifi + Cellular",
                "sku_append" => ".WIFI.CELLULAR",
                "slug_append" => "-wifi-cellular",
                "description" => "Wifi & Cellular connectivity"
            ]
        ]
    ];

    foreach($variations as $variationName => $variation) {

        // Create the variation
        $storedVariation = $moltin->variations->create([
            "type" => "product-variation",
            "name" => $variationName
        ])->data();

        $variationRelationships = [];

        foreach($variation as $slug => $config) {

            // Create the product name modifier
            $nameModifier = $moltin->modifiers->create([
                'type' => 'product-modifier',
                'modifier_type' => 'name_append',
                'value' => $config['name_append']
            ])->data();

            // Create the product SKU modifier
            $skuModifier = $moltin->modifiers->create([
                'type' => 'product-modifier',
                'modifier_type' => 'sku_append',
                'value' => $config['sku_append']
            ])->data();

            // Create the product slug modifier
            $slugModifier = $moltin->modifiers->create([
                'type' => 'product-modifier',
                'modifier_type' => 'slug_append',
                'value' => $config['slug_append']
            ])->data();

            // Create the variation option
            $option = $moltin->variationOptions->create([
                'type' => 'product-variation-option',
                'name' => $config['name'],
                'description' => $config["description"]
            ])->data();

            // link the variation to the option
            $moltin->variations->createRelationships($storedVariation->id, 'variation-options', [$option->id]);

            // Link the modifer to the option
            $r = $moltin->variationOptions->createRelationships($option->id, 'product-modifiers', [$nameModifier->id, $skuModifier->id, $slugModifier->id]);

            // Link to the Variation
            $variationRelationships[] = $option->id;
        }

        $moltin->products->createRelationships($base->id, 'variations', [$storedVariation->id]);
    }


} catch(Exception $e) {

    echo 'An exception occurred calling the moltin API:';
    var_dump($e);
    exit;

}
