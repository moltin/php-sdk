<?php

require_once('./init.php');

try {

    function printCartItemsTable($items) {

        $table = new Console_Table();
        $table->setHeaders(['ID', 'Product', 'Quantity', 'Unit Price', 'Value']);
        $i = 0;
        foreach($items as $item) {
            $table->addRow([
                $item->id,
                empty($item->product_id) ? 'custom' : $item->product_id,
                $item->quantity,
                $item->unit_price->amount . " (" . $item->unit_price->currency . ")",
                $item->value->amount . " (" . $item->value->currency . ")",
            ]);
            if ($i < count($items) - 1) {
                $table->addSeparator();
            }
            $i++;   
        }
        echo $table->getTable();
    }

    function printOrderTable($order) {

        $table = new Console_Table();
        $table->setHeaders(['ID', 'Status', 'Payment', 'Shipping', 'Value']);
        $table->addRow([
            $order->id,
            $order->status,
            $order->payment,
            $order->shipping,
            $order->meta->display_price->with_tax->formatted,
        ]);
        echo $table->getTable();
    }

    $startTime = microtime(true);

    // known cart id (usually stored in cookie - we're setting it here because we're on the CLI)
    $cartID = '95597f65a5ea7e907a4dcbe4eb6b4435';

    $stamp = time();

    // add a product to the cart
    $productOne = $moltin->products->create([
        'type' => 'product',
        'name' => 'My Great Product',
        'slug' => 'my-great-product-' . $stamp,
        'sku' => 'my.great.product.' . $stamp,
        'manage_stock' => false,
        'description' => 'My Great Product is awesome',
        'price' => [
            [
                'amount' => 5000,
                'currency'=> 'USD',
                'includes_tax' => true
            ]
        ],
        'status' => 'live',
        'commodity_type' => 'physical',
    ]);
    $productOneID = $productOne->data()->id;
    $productQuantity = 2;
    echo "\n\nAdding " . $productQuantity . " x " . $productOneID . " to your cart. ";
    $cart = $moltin->cart($cartID)->addProduct($productOneID, $productQuantity);
    echo "Cart value (with tax) is now: " . $cart->meta()->display_price->with_tax->formatted . "\n";
    printCartItemsTable($cart->data());

    $productTwo = $moltin->products->create([
        'type' => 'product',
        'name' => 'My Even Greater Product',
        'slug' => 'my-even-greater-product-' . $stamp,
        'sku' => 'my.even.greater.product.' . $stamp,
        'manage_stock' => false,
        'description' => 'My Even Greater Product is even more awesome',
        'price' => [
            [
                'amount' => 10000,
                'currency'=> 'USD',
                'includes_tax' => true
            ]
        ],
        'status' => 'live',
        'commodity_type' => 'physical',
    ]);
    $productTwoID = $productTwo->data()->id;
    $productTwoQuantity = 3;
    echo "\n\nAdding " . $productTwoQuantity . " x " . $productTwoID . " to your cart.";
    $cart = $moltin->cart($cartID)->addProduct($productTwoID, $productTwoQuantity);
    echo " Cart value (with tax) is now: " . $cart->meta()->display_price->with_tax->formatted . "\n";
    printCartItemsTable($cart->data());

    // add a custom item
    $cart = $moltin->cart($cartID)->addCustomItem("Custom Item", "custom.item", "An example custom item", 100, 2);
    echo " Cart value (with tax) is now: " . $cart->meta()->display_price->with_tax->formatted . "\n";
    printCartItemsTable($cart->data());
    
    // get the cart items manually
    $items = $moltin->cart($cartID)->items()->data();

    // update the qty of an item
    $newQuantity = 4;
    echo "\n\nUpdating first items quantity to {$newQuantity}:\n";
    $items = $moltin->cart($cartID)->updateItemQuantity($items[0]->id, $newQuantity)->data();
    printCartItemsTable($items);

    // remove the second item
    echo "\n\nRemoving second item:\n";
    $items = $moltin->cart($cartID)->removeItem($items[1]->id)->data();
    printCartItemsTable($items);

    // checkout the cart (convert to an order)
    $customer = [
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ];
    $billingAddress = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'line_1' => '123 Example Street',
        'line_2' => 'WhosVille',
        'county' => 'Somewhere',
        'postcode' => 'EX12 3AM',
        'country' => 'UK'
    ];
    $shippingAddress = $billingAddress; // ship to the billing address

    // $order will be a Moltin\Entities\Order object, not a \Moltin\Response
    echo "\n\nChecking out cart to order:\n";
    $order = $moltin->cart($cartID)->checkout($customer, $billingAddress, $shippingAddress);

    // now we have an order, make the payment via stripe (card payment)
    $payment = $order->pay('stripe', 'purchase', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'number' => '4242424242424242',
        'month' => 05,
        'year' => date('Y', strtotime('+1 year')),
        'verification_value' => 213
    ]);

    // get the full order info and view it
    $orderResponse = $moltin->orders->get($order->getID());
    printOrderTable($orderResponse->data());

    $endTime = microtime(true);
    echo "\n\n[Example completed in " . round(($endTime - $startTime), 5) . " seconds]\n";

} catch(Exception $e) {

    echo 'An exception occurred calling the moltin API:';
    var_dump($e->getMessage());
    exit;

}
