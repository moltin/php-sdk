# moltin PHP SDK

## Instantiating the SDK Client:

Pass in the configuration to the client:

```php
$config = [
    'client_id' => '{your_client_id}',
    'client_secret' => '{your_client_secret}',
    'currency_code' => 'USD',
    'language' => 'en',
    'locale' => 'en_gb'
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
            ->setLocale('en_gb');
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
$cartID = 'a_unique_refeference';
$moltin->cart($cartID)->addProduct($productID); // adds 1 x $productID
$moltin->cart($cartID)->addProduct($productID), 3; // adds 3 x $productID (now has 4)
```

Get the cart items:

```php
$moltin->cart($cartID)->items();
```

Update the quantity of an item in the cart:

```php
$moltin->cart($cartID)->updateItemQuantity($cartItemID, 2); // now has 2
```

Remove an item from the cart:

```php
$moltin->cart($cartID)->removeItem($cartItemID);
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
$order = $moltin->cart($cartID)->checkout($customer, $billing, $shipping);
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
