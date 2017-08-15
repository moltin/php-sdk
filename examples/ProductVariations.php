<?php

require_once('./init.php');

try {

    // reset resources (optional)
    // foreach($moltin->products->all()->data() as $product) {
    //     $moltin->products->delete($product->id);
    // }
    // foreach($moltin->variations->all()->data() as $variation) {
    //     $moltin->variations->delete($variation->id);
    // }

    // this example follows the documentation here:
    // https://moltin.api-docs.io/v2/product-variations/variations-example

    $startTime = microtime(true);
    $calls = 0;

    // Create the base product
    $base = $moltin->products->create([
        "type" => "product",
        "name" => "iPad Mini 4",
        "slug" => "ipad-mini-4",
        "sku" => "IPAD-MINI-4",
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
    $calls++;

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
            "32gb" => [
                "name" => "32GB",
                "name_append" => " 32GB",
                "sku_append" => ".32GB",
                "slug_append" => "-32",
                "description" => "32GB Storage"
            ],
            "64gb" => [
                "name" => "64GB",
                "name_append" => " 64GB",
                "sku_append" => ".64GB",
                "slug_append" => "-64",
                "description" => "64GB Storage"
            ],
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
                "sku_append" => ".WIFI+CELLULAR",
                "slug_append" => "-wifi-cellular",
                "description" => "Wifi & Cellular connectivity"
            ]
        ]
    ];

    foreach($variations as $variationName => $variation) {

        // Create the variation
        $variationID = $moltin->variations->create([
            "type" => "product-variation",
            "name" => $variationName
        ])->data()->id;
        $calls++;
        
        $variationRelationships = [];

        foreach($variation as $slug => $config) {

            // Create the variation option
            foreach($moltin->variations->createOption($variationID, [
                'name' => $config['name'],
                'description' => $config["description"]
            ])->data()->options as $o) {
                if ($o->name === $config['name']) {
                    $option = $o;
                }
            }
            $calls++;
            
            // Create the product name modifier
            $nameModifier = $moltin->variations->createModifier(
                $variationID,
                $option->id,
                [
                    'modifier_type' => 'name_append',
                    'value' => $config['name_append']
                ]
            )->data();
            $calls++;
            
            // Create the product SKU modifier
            $skuModifier = $moltin->variations->createModifier(
                $variationID,
                $option->id,
                [
                    'modifier_type' => 'sku_append',
                    'value' => $config['sku_append']
                ]
            )->data();
            $calls++;
            
            // Create the product slug modifier
            $slugModifier = $moltin->variations->createModifier(
                $variationID,
                $option->id,
                [
                    'modifier_type' => 'slug_append',
                    'value' => $config['slug_append']
                ]
            )->data();
            $calls++;
        }

        // link the product to the variation to the option
        $moltin->products->createRelationships($base->id, 'variations', [$variationID]);
        $calls++;
    }

    // build the child products
    $moltin->products->build($base->id);
    $calls++;
    
    $products = $moltin->products->all()->data();

    $table = new Console_Table();
    $table->setHeaders(['Name', 'SKU']);

    $i = 0;
    foreach($products as $product) {
        $table->addRow([
            $product->name,
            $product->sku,
        ]);
        if ($i < count($products) - 1) {
            $table->addSeparator();
        }
        $i++;
    }

    $endTime = microtime(true);
    echo "[Created " . count($products) . " products ({$calls} API calls) in " . round(($endTime - $startTime), 5) . " seconds]\n\n";

    echo $table->getTable();

} catch(Exception $e) {

    echo 'An exception occurred calling the moltin API:';
    var_dump($e);
    exit;

}
