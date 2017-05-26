<?php

require_once('./init.php');

try {

    $productID = false;
    $categoryIds = [];

    $cleanup = true;

    // create a product
    $unique = sha1(rand(0,5000) . microtime());
    $productCreate = $moltin->products->create([
        'type' => 'product',
        'name' => 'My Product ' . $unique,
        'slug' => 'my-product ' . $unique,
        'sku' => 'my.prod.'.$unique,
        'description' => 'a bit about it',
        'manage_stock' => false,
        'status' => 'live',
        'commodity_type' => 'digital'
    ]);

    if ($productCreate->getStatusCode() === 201) {

        $productID = $productCreate->data()->id;
        echo "Product ($productID) created (" . $productCreate->getExecutionTime() . "s)\n";

        // create a category
        $categoryCreate = $moltin->categories->create([
            'type' => 'category',
            'name' => 'My First Category',
            'slug' => 'my-first-category-' . $unique
        ]);

        if ($categoryCreate->getStatusCode() === 201) {

            $categoryId = $categoryCreate->data()->id;
            $categoryIds[] = $categoryId;
            echo "Category ($categoryId) created (" . $categoryCreate->getExecutionTime() . "s)\n";

        }

        // create a second category
        $categoryCreate = $moltin->categories->create([
            'type' => 'category',
            'name' => 'My Second Category',
            'slug' => 'my-second-category-' . $unique
        ]);

        if ($categoryCreate->getStatusCode() === 201) {

            $categoryId = $categoryCreate->data()->id;
            $categoryIds[] = $categoryId;
            echo "Category ($categoryId) created (" . $categoryCreate->getExecutionTime() . "s)\n";

        }

        // create a relationship between the first category and the product
        $relationshipCreate = $moltin->products->createRelationships($productID, 'categories', [$categoryIds[0]]);

        // create a relationship between the second category and the product
        $relationshipCreate = $moltin->products->createRelationships($productID, 'categories', [$categoryIds[1]]);

        // delete the relationship between the product and the first category (inferred using UPDATE)
        $relationshipUpdate = $moltin->products->updateRelationships($productID, 'categories', [$categoryIds[1]]);

        // include the category data
        $includeResponse = $moltin->products->with(['categories'])->get($productID);
        $cats = $includeResponse->included();

        // delete the relationship between the product and the second category (using DELETE)
        $relationshipDelete = $moltin->products->deleteRelationships($productID, 'categories', [$categoryIds[0]]);

    }

    // clean up
    if ($cleanup) {

        // remove the product
        $deleteProduct = $moltin->products->delete($productID);
        if ($deleteProduct->getStatusCode() === 200) {
            echo "Product ($productID) deleted (" . $deleteProduct->getExecutionTime() . "s)\n";
        }

        // remove the categories
        foreach($categoryIds as $categoryId) {
            $deleteCategory = $moltin->categories->delete($categoryId);
            if ($deleteCategory->getStatusCode() === 200) {
                echo "Category ($categoryId) deleted (" . $deleteCategory->getExecutionTime() . "s)\n";
            }
        }
    }

} catch(Exception $e) {

    echo 'An exception occurred calling the moltin API:';
    var_dump($e);
    exit;

}
