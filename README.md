# PHP SDK

* [Website](http://molt.in)
* [License](https://github.com/moltin/php-sdk/master/LICENSE)
* Version: dev

The Moltin php-sdk is a simple to use interface for the API to help you get off the ground quickly and efficiently.

## Installation
Download and install composer from `http://www.getcomposer.org/download`

Add the following to your project `composer.json` file
```
{
    "require": {
        "moltin/php-sdk": "dev-master"
    }
}
```
When you're done just run `php composer.phar install` and the package is ready to be used.

## Usage

Below is a basic usage guide for this package.

### Instantiating the Package

Before you begin you will need to instantiate the package.

``` php
use Moltin\SDK\Request\CURL as Request;
use Moltin\SDK\Storage\Session as Storage;

$moltin = new \Moltin\SDK\SDK(new Storage(), new Request());
```

If you wish to use another storage or request method simply change the relevant use statement to reflect your preferences.

### Authorisation

Before you can use the API you will need to authorise, there are a number of ways to this. The simplest of which is to use the "client credentials" method. You can do this as follows:

``` php
$result = $moltin->authenticate(new \Moltin\SDK\Authenticate\ClientCredentials(), array(
	'client_id'     => '<YOUR CLIENT ID>',
	'client_secret' => '<YOUR CLIENT SECRET>'
));
```

Once this is done your token will be stored in your selected storage method and passed automatically to all subsequent calls.

### Making a Call

After authorising you can start to make calls to the API, there are four simple calls to use: GET, PUT, POST and DELETE.

*Note:* The following example shows the products API, for other end-points please check our [Documentation](http://docs.molt.in)

``` php
	// Create a product
	$result = $moltin->post('product', $_POST);

	// Update a product
	$result = $moltin->put('product/1', array('title' => 'Updated!'));

	// Get a product
	$result = $moltin->get('product/1');

	// Delete a product
	$result = $moltin->delete('product/1');
```

### Building a Form

To help with the usual CRUD forms we've included an automated form builder to take care of the messy bits for you.

``` php
	// Get fields (create)
	$fields = $moltin->fields('products');

	// Get fields (edit product 1)
	$fields = $moltin->fields('products', 1);

	// Show form
	foreach ($fields as $field) {
		echo '<label for="'.$field['slug'].'">'.$field['name'].'</label>';
		echo $field['input'];
	}
```

## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


## Credits

- [Moltin](https://github.com/moltin)
- [All Contributors](https://github.com/moltin/php-sdk/contributors)


## License

Please see [License File](LICENSE) for more information.
