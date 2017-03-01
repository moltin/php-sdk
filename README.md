# moltin PHP SDK

## Installation

You can install the package manually or by adding it to your `composer.json`:

```
{
  "require": {
      "moltin/php-sdk": "v2.x-dev"
  }
}
```

## Instantiating the SDK Client:

Pass in the configuration to the client:

```php
$config = [
    'client_id' => '{your_client_id}',
    'client_secret' => '{your_client_secret}',
    'currency_code' => 'USD',
    'language' => 'en',
    'locale' => 'en_gb',
    'cookie_cart_name' => 'moltin_cart_reference',
    'cookie_lifetime' => '+28 days'
];
$moltin = new Moltin\Client($config);
```

Or configure after construct:

```php
$moltin = new Moltin\Client()
            ->setClientID('xxx')
            ->setClientSecret('yyy')
            ->setCurrencyCode('USD')
            ->setLanguage('en')
            ->setLocale('en_gb')
            ->setCookieCartName('moltin_cart_reference')
            ->setCookieLifetime('+28 days');
```

**Note:** if you are unsure what your `client_id` or `client_secret` are, please select the
[store in your account](https://accounts.moltin.com) and copy them.

## Enterprise Customers

If you are an enterprise customer and have your own infrastructure with your own domain, you can configure the client to use your domain:

```php
$moltin->setBaseURL('https://api.yourdomain.com');
```

Or by adding the `api_endpoint` field to the `$config` array you pass to the constructor.

## Using the client

### Multiple Resources

To return a list of your resources (limited to 100 depending your [store configuration] (https://moltin.api-docs.io/v2/settings)):

```php
// return a list of your products 
$moltin->products->all();

// return your brands
$moltin->brands->all();

// return your categories
$moltin->categories->all();

// return your collections
$moltin->collections->all();

// return your files
$moltin->files->all();
```

### Single Resource by ID

Fetch a Resource by ID:

```php
$moltin->products->get($productID);
```

### Fetching the category/brand/collection tree

Categories, brands and collections can be nested to create a tree structure (see the [CategoryRelationships](examples/CategoryRelationships.php) example).

You can retrieve a full tree of the items rather than having to build them by using `tree` method:

```php
$moltin->categories->tree();
```

### Limiting and Offsetting Results

Limit the number of resources returned:

```php
$moltin->products->limit(10)->all();
```

Offset the results (page 2):

```php
$moltin->products->limit(10)->offset(10)->all();
```

### Sorting Results

Order by `name`:

```php
$moltin->products->sort('name')->all();
```

Reversed:

```php
$moltin->products->sort('-name')->all();
```

### Filtering Results

To [filter your results](https://moltin.api-docs.io/v2/using-the-api/filtering) when calling resources which support it (e.g. `/v2/products`).

A simple filter to get all products which are in stock may look like this:

```php
$moltin->products->filter([
    ['gt' => ['stock' => 0]]
])->all();
```

A more advanced filter to find products which are digital, drafted and have a stock greater than 20 would look like this:

```php
$moltin->products->filter([
    ['eq' => ['status' => 'draft']],
    ['eq' => ['commodity_type' => 'digital']],
    ['gt' => ['stock' => 20]]
])->all();
```

The `array` passed to the `filter` method should contain all of the conditions required to be met by the filter on the API and allow you to use several filters of the same type (as demostrated above).

For more information on the filter operations please read the [API reference](https://moltin.api-docs.io/v2/using-the-api/filtering).

### Including data

To include other data in your request (such as products when getting a category) call the `with()` method on the resource:

```php
$response = $moltin->categories->with(['products'])->get($categoryID);
$category = $response->data();
$products = $response->included()->products;
```

### Create Relationships

To create relationships between resources:

```php
$moltin->products->createRelationships($productID, 'categories', [$categoryID]);
```

To delete a relationship between resources:

```php
$moltin->products->deleteRelationships($productID, 'categories', [$categoryID]);
```

Or an update with an empty array achieves the same result if you're so inclined:

```php
$moltin->products->updateRelationships($productID, 'categories', []);
```

### Requesting a Specific Currency

For calls that support the `X-MOLTIN-CURRENCY` header, you can specifiy it on the client:

```php
$moltin->currency('USD')->products->all();
$moltin->currency('GBP')->products->all();
```

### Working with files

A `POST` request to the `v2/files` endpoint allows you to upload a file and store it remotely.

To create a file using the SDK, you need to have the file on disk:

```php
// create a file from a local disk
$moltin->files->create(['public' => 'true'], '/path/to/file.jpg');

// create a file from a URL (note: this will download the file to your local disk then upload)
$moltin->files->create(['public' => 'true'], 'https://placeholdit.imgix.net/~text?&w=350&h=150');
```

### Carts, Orders and Payments

To simplify the way you process carts, orders and payments, we provide some utility functions.

#### Carts

Adding items to a cart:

```php
$cartReference = 'a_unique_refeference'; // supply a custom cart reference
$moltin->cart($cartReference)->addProduct($productID); // adds 1 x $productID
$moltin->cart($cartReference)->addProduct($productID, 3); // adds 3 x $productID (now has 4)
```

When no cart reference is supplied, we will get it from the `$_COOKIE`. You can change the name used in the `$_COOKIE` by passing it in the config when instantiating the client.

This is therefore a valid call (although the cart will be a new one if you follow on from the example above):

```php
$moltin->cart()->addProduct($productID);
```

Get the cart items:

```php
foreach($moltin->cart()->items() as $item) {
    $cartItemID = $item->id;
    // ... do something
    echo $item->name . "\n";
}
```

Update the quantity of an item in the cart:

```php
$moltin->cart()->updateItemQuantity($cartItemID, 2); // now has 2
```

Remove an item from the cart:

```php
$moltin->cart()->removeItem($cartItemID);
```

#### Orders

To convert a cart to an order (which can then be paid):

```php
$customer = [ 
    // ... customer data
];
$billing = [
    // ... billing data
];
$shipping = [
    // ... shipping data
];
$order = $moltin->cart($cartReference)->checkout($customer, $billing, $shipping);
```

#### Payments

When you have a `Moltin\Entities\Order` object from the checkout method you can take a payment for it:

```php
$gatewaySlug = 'stripe'; // the slug of your payment gateway
$paymentMethod = 'purchase'; // the order payment method (purchase is supported for now)
$params = []; // payment params (these will vary depending on the gateway, check out the example for Stripe and the docs for others)
$payment = $order->pay($gatewaySlug, $paymentMethod, $params);
```

You can now check the response (`$payment`) to see what happened with your payment.

You can also setup test details for most payment gateways, please refer to them for their details and check out the [example](examples/Carts.php) for more informatiom on `cart -> order -> pay`.

## Handling Exceptions

Aside from errors that may occur due to the call, there may be other Exceptions thrown. To handle them, simply wrap your call in a try catch block:

```php
try {
    $moltin->products->all();
} catch (Exception $e) {
    // do something with $e
}
```

Internally, there are several custom Exceptions which may be raised - see the [Exceptions](src/Exceptions) directory for more information.

## Examples

In the `examples` directory there are command line implementations using the SDK. To use the examples you will need to:

 - Install dependencies with ```composer install```
 - Copy the `examples/.env.tmpl` to `examples/.env` and add your credentials to it.
 - Run the example file you want, for example: ```php ./ProductList.php```

By default, for successful calls, we will display the data in a table on the command line. You can, however, use a flag to view the response:

```php ./ProductList.php format=json```

## Test

```bash
phpunit
```

Generate a coverage report:

```bash
phpunit --coverage-html ./ignore
```
