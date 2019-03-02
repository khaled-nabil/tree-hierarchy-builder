<?php

namespace Tests\Functional;

class PostHierarchyTest extends BaseTestCase
{
	private $token = null;
	
	public function __construct()
    {
        $this->token = "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImFkbWluIn0.mgxgA4hKW-j4dZtbRcYIvHFwt4Ewq8eQsXbpYpF9FuY";
    }

    public function testShouldBe401WithoutAuthentication()
    {
        $response = $this->runApp('POST', '/hierarchy');
        $this->assertEquals(401, $response->getStatusCode());
    }
    public function testShouldBe401WrongPassword()
    {
		$credentials = json_decode('{"username": "admin", "password":""}',true);
		$response = $this->runApp('POST', '/login', $credentials);
        $this->assertEquals(401, $response->getStatusCode());
    }	
	public function testShouldBe200CorrectLogin(){
		$credentials = json_decode('{"username": "admin", "password":"123456"}',true);
		$response = $this->runApp('POST', '/login', $credentials);
        $this->assertEquals(200, $response->getStatusCode());

	}
    public function testShouldBe400IfNoPayload()
    {
        $response = $this->runApp('POST', '/hierarchy', null, $this->token);
        $this->assertEquals(400, $response->getStatusCode());
        // $this->assertEquals(404, $response->getStatusCode());

        // $responseJson = json_decode((string)$response->getBody(), true);

        // $this->assertFalse($responseJson['success']);
    }

    public function testPostHierarchy()
    {
        $payload = json_decode('{
    "Pete": "Nick", 
    "Barbara": "Nick", 
    "Nick": "Sophie", 
    "Sophie": "Jonas"
}', true);
        $expected = json_decode('{
"Jonas": {
   "Sophie": {
       "Nick": {
           "Pete": [],
           "Barbara": []
       }
   }
}
}', true);
        $response = $this->runApp('POST', '/hierarchy', $payload, $this->token);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expected, json_decode((string)$response->getBody(), true));
    }

    public function testPostHierarchyLoop()
    {
        $payload = json_decode('{
    "Pete": "Nick", 
    "Barbara": "Nick", 
    "Nick": "Sophie", 
    "Sophie": "Pete"
}', true);

        $response = $this->runApp('POST', '/hierarchy', $payload, $this->token);

        $this->assertEquals(422, $response->getStatusCode());
    }
}