<?php

namespace Tests\Functional;

class PostHierarchyTest extends BaseTestCase
{
    public function testShouldBe404IfNotFound()
    {
        $response = $this->runApp('POST', '/hierarchy');

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
        $response = $this->runApp('POST', '/hierarchy', $payload);

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

        $response = $this->runApp('POST', '/hierarchy', $payload);

        $this->assertEquals(422, $response->getStatusCode());
    }
}