<?php

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App();
$app->add(
    new \Slim\Middleware\Session([
        'name' => 'dummy_session',
        'autorefresh' => true,
        'lifetime' => '1 hour',
    ])
);

$app->get('/', function ($req, $res) {
    // or $this->session if registered
    $session = new \SlimSession\Helper();

    // Check if variable exists
    $exists = $session->exists('my_key');
    $exists = isset($session->my_key);
    $exists = isset($session['my_key']);

    // Get variable value
    $my_value = $session->get('my_key', 'default');
    $my_value = $session->my_key;
    $my_value = $session['my_key'];

    // Set variable value
    $session->set('my_key', 'my_value');
    $session->my_key = 'my_value';
    $session['my_key'] = 'my_value';

    // Merge value recursively
    $session->merge('my_key', ['first' => 'value']);
    $session->merge('my_key', ['second' => ['a' => 'A']]);
    $letter_a = $session['my_key']['second']['a']; // "A"

    // Delete variable
    $session->delete('my_key');
    unset($session->my_key);
    unset($session['my_key']);

    // Destroy session
    $session::destroy();

    // Get session id
    $id = $session::id();

    return $res;
});