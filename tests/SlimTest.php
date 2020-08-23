<?php

use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

class SlimTest extends TestCase
{
    protected $app;

    public function __construct()
    {
        parent::__construct();
        $this->app = $this->runApp();
    }

    public function runApp()
    {
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

        return $app;
    }

    public function processRequest($requestMethod, $requestUri, $requestData = null)
    {
        // Create a mock environment for testing with
        $environment = Environment::mock(
            [
                'REQUEST_METHOD' => $requestMethod,
                'REQUEST_URI' => $requestUri
            ]
        );

        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);

        // Add request data, if it exists
        if (isset($requestData)) {
            $request = $request->withParsedBody($requestData);
        }

        // Set up a response object
        $response = new Response();

        // Process the application
        $response = $this->app->process($request, $response);

        // Return the response
        return $response;
    }

    public function testSlim()
    {
        $response = $this->processRequest('GET', '/');
        echo $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());
    }
}