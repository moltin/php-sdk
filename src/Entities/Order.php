<?php

namespace Moltin\Entities;

use Moltin\Entities\Cart as Cart;
use Moltin\Resources\Orders as Orders;

class Order extends Orders
{
    private $id;
    private $cart;
    private $data;

    /**
     *  Set the order ID
     *
     *  @param string $id
     *  @return $this
     */
    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     *  Get the order ID
     *
     *  @return string
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     *  Set the order data
     *
     *  @param object $data
     *  @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     *  Get the order data
     *
     *  @return object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     *  Set the orders cart
     *
     *  @param Moltin\Entities\Cart $cart
     *  @return $this
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
        return $this;
    }

    /**
     *  Get the orders cart
     *
     *  @return Moltin\Entities\Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     *  Pay for the order
     *
     *  @param string $gateway the slug of the payment gateway
     *  @param string $method the order method (eg 'purchase')
     *  @param array $paymentParams payment params to use in the payment (eg card details, but this depends on the $gateway selected - see docs for more info)
     *  @return Moltin\Response
     */
    public function pay($gateway, $method, $paymentParams)
    {
        return $this->call('POST', ['data' => $this->mergePayData($gateway, $method, $paymentParams)], $this->getID() . '/payments');
    }

    /**
     *  Merge pay data to join API payload/SDK utility
     *
     *  @param string $gateway the slug of the payment gateway
     *  @param string $method the order method (eg 'purchase')
     *  @param array $paymentParams payment params to use in the payment (eg card details, but this depends on the $gateway selected - see docs for more info)
     *  @return array
     */
    public function mergePayData($gateway, $method, $paymentParams)
    {
        $data = [];
        $data['gateway'] = $gateway;
        $data['method'] = $method;
        return array_merge($data, $paymentParams);
    }
}
