<?php

session_id() || session_start();

require '../vendor/autoload.php';

use Moltin\Client;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$config = [
    'client_id' => getenv('CLIENT_ID'),
    'client_secret' => getenv('CLIENT_SECRET'),
    'currency_code' => getenv('CURRENCY_CODE'),
    'language' => getenv('LANGUAGE'),
    'locale' => getenv('LOCALE'),
    'api_endpoint' => getenv('API_ENDPOINT')
];

$moltin = new Client($config);
