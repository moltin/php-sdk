# PHP SDK

* [Website](http://moltin.com)
* [License](https://github.com/moltin/php-sdk/master/LICENSE)
* Version: dev

The Moltin php-sdk is a simple to use interface for the API to help you get off the ground quickly and efficiently.

## Installation
Download and install composer from `http://www.getcomposer.org/download`

Run the following command:
```
$ composer require moltin/php-sdk
```

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
$result = \Moltin::Authenticate('ClientCredentials', [
        'client_id'     => '<YOUR CLIENT ID>',
        'client_secret' => '<YOUR CLIENT SECRET>',
]);
```

Once this is done your token will be stored in your selected storage method and passed automatically to all subsequent calls.

### Making a Call

After authorising you can start to make calls to the API, there are four simple calls to use: GET, PUT, POST and DELETE.

*Note:* The following example shows the products API, for other end-points please check our [Documentation](http://docs.molt.in)

``` php
	// Create a product
	$result = \Product::Create($_POST);

	// Update a product
	$result = \Product::Update('<ID>', array('title' => 'Updated!'));

	// Get a product
	$result = Product::Get('<ID>');

	// Delete a product
	$result = Product::Delete('<ID>');
```

### Building a Form

To help with the usual CRUD forms we've included an automated form builder to take care of the messy bits for you.

``` php
	// Get fields (create)
	fields = \Product::Fields();

	// Get fields (edit product 1)
	fields = \Product::Fields('<ID>');

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
