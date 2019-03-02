<?php

namespace Tests\Functional;

use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * This is an example class that shows how you could set up a method that
 * runs the application. Note that it doesn't cover all use-cases and is
 * tuned to the specifics of this skeleton app, so if your needs are
 * different, you'll need to change it.
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Use middleware when running application?
     *
     * @var bool
     */
    protected static $withMiddleware = true;

    /** @var App */
    protected static $app;

    public static function setUpBeforeClass()
    {
        // Use the application settings
        $settings = require __DIR__ . '/../../src/settings.php';
        $testSettings = require __DIR__ . '/../../src/settings.test.php';

        // Instantiate the application
        $app = static::$app = new App(array_replace_recursive($settings, $testSettings));

        // Set up dependencies
        require __DIR__ . '/../../src/dependencies.php';

        // Register middleware
        if (static::$withMiddleware) {
            require __DIR__ . '/../../src/middleware.php';
        }

        // Register routes
        require __DIR__ . '/../../src/routes.php';
    }

    public function runApp($requestMethod, $requestUri, $requestData = null, $requestAuthHeader = null)
    {
		$enviromentData = [
                'REQUEST_METHOD' => $requestMethod,
                'REQUEST_URI' => $requestUri
            ];
			
		// if authentication is set
		if(isset($requestAuthHeader))
			$enviromentData['HTTP_AUTHORIZATION'] = $requestAuthHeader;
	
        // Create a mock environment for testing with
        $environment = Environment::mock($enviromentData);

        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);

        $request->withHeader('Content-Type', 'application/json');
        
        // Add request data, if it exists
        if (isset($requestData)) {
            $request = $request->withParsedBody($requestData);
        }

        // Process the application
        $response = static::$app->process($request, new Response());

        // Return the response
        return $response;
    }
}
