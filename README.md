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

### All Resource

To return a list of your resources (limited to 100 depending your [store configuration] (https://moltin.api-docs.io/v2/settings)):

```php
// return a list of your products 
$moltin->products->get();

// return your brands
$moltin->brands->get();

// return your categories
$moltin->categories->get();

// return your collections
$moltin->collections->get();

// return your files
$moltin->files->get();
```

### Resources by ID

Fetch a Resource by ID:

```php
$moltin->products->get($productID);
```

### Fetching the category/brand/collection tree

Categories, brands and collections can be nested to create a tree structure (see the CategoryRelationships example).

You can retrieve a full tree of the items rather than having to build them by using `tree` in the get call:

```php
$moltin->categories->get('tree');
```

### Limiting and Offsetting Results

Limit the number of resources returned:

```php
$moltin->products->limit(10)->get();
```

Offset the results (page 2):

```php
$moltin->products->limit(10)->offset(10)->get();
```

### Sorting Results

Order by `name`:

```php
$moltin->products->sort('name')->get();
```

Reversed:

```php
$moltin->products->sort('-name')->get();
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
$moltin->currency('USD')->products->get();
$moltin->currency('GBP')->products->get();
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
