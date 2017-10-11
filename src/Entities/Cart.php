<?php

namespace Moltin\Entities;

use Moltin\Entities\Order as Order;
use Moltin\Resources\Carts as Carts;
use Moltin\Exceptions\UnableToCheckoutException as UnableToCheckoutException;

class Cart extends Carts
{
    private $reference;

    public function __construct($cartID, $client = false, $requestLibrary = false, $storage = false)
    {
        if (!$cartID) {
            $cartID = $this->getReference($client);
        }
        $this->setReference($cartID);
        return parent::__construct($client, $requestLibrary, $storage);
    }

    /**
     *  Create a unique cart reference
     *
     *  @return string
     */
    public function createReference()
    {
        return md5(uniqid());
    }

    /**
     *  Set the cart reference
     *
     *  @param string $reference
     *  @return $this
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     *  Get the cart reference
     *  If it isn't set or does not exist in storage, we will create a new one and add it
     *
     *  @return string
     */
    public function getReference($client = false)
    {
        if (empty($this->reference)) {

            $reference = $this->createReference();

            if ($client) {
                if (isset($_COOKIE[$client->getCookieCartName()])) {
                    $reference = $_COOKIE[$client->getCookieCartName()];
                } else {
                    setcookie($client->getCookieCartName(), $reference, strtotime($client->getCookieLifetime()), '/');
                }
            }
            $this->setReference($reference);
        }

        return $this->reference;
    }

    /**
     *  Add a Product to the Cart
     *
     *  @param string $id the Product ID
     *  @param int $qty the quantity to add to the cart
     *  @param array $spec the product spec (variations, inputs, singles etc)
     *  @return Moltin\Response
     */
    public function addProduct($id, $qty = 1, $spec = [])
    {
        $body = [
            'data' => [
                'type' => 'cart_item',
                'id' => $id,
                'quantity' => $qty
            ]
        ];
        return $this->call('POST', $body, $this->getReference() . '/items');
    }

    /**
     *  Add a Custom Item to the Cart
     *
     *  @param string $name the custom item name
     *  @param string $sku the custom item sku
     *  @param string $description the custom item description
     *  @param int $price
     *  @param int $qty the quantity to add to the cart
     *  @return Moltin\Response
     */
    public function addCustomItem($name, $sku, $description, $price, $qty = 1)
    {
        $body = [
            'data' => [
                'type' => 'custom_item',
                'name' => $name,
                'sku' => $sku,
                'description' => $description,
                'quantity' => $qty,
                'price' => [
                    'amount' => $price
                ]
            ]
        ];
        return $this->call('POST', $body, $this->getReference() . '/items');
    }

    /**
     *  Get the items in a cart
     *
     *  @return Moltin\Response
     */
    public function items()
    {
        return $this->call('GET', false, $this->getReference() . '/items');
    }

    /**
     *  Update an items quantity in a cart
     *
     *  @param string $id the cart item idate
     *  @param int $quantity the new quantity
     *  @return Moltin\Response
     */
    public function updateItemQuantity($id, $quantity)
    {
        $body = [
            'data' => [
                'id' => $id,
                'quantity' => $quantity
            ]
        ];
        return $this->call('PUT', $body, $this->getReference() . "/items/{$id}");
    }

    /**
     *  Remove an item from the cart
     *
     *  @param string $id the cart item id to remove
     *  @return Moltin\Response
     */
    public function removeItem($id)
    {
        return $this->call('DELETE', false, $this->getReference() . "/items/{$id}");
    }

    /**
     *  Checkout a cart (convert it to an order)
     *
     *  @param array $customer the customer data
     *  @param array $billing the billing data
     *  @param array $shipping the shipping data
     *  @return Moltin\Entities\Order
     *  @throws Moltin\Exceptions\UnableToCheckoutException
     */
    public function checkout($customer, $billing, $shipping = false)
    {
        if (!$shipping) {
            $shipping = $billing;
        }
        $response = $this->call('POST', [
            'data' => [
                'customer' => $customer,
                'billing_address' => $billing,
                'shipping_address' => $shipping
            ]
        ], $this->getReference() . '/checkout');

        if ($response->getStatusCode() === 201) {
            $order = new Order($this->getClient());
            $order->setID($response->data()->id);
            $order->setData($response->data());
            $order->setCart($this);
            return $order;
        } else {
            throw new UnableToCheckoutException();
        }
    }

}
