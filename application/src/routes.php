<?php
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

$app->post('/hierarchy', \Personia\Controllers\PostHierarchyController::class);

$app->get('/supervisor/{name}', \Personia\Controllers\GetHierarchyController::class);

$app->post('/login', function (Request $request, Response $response, array $args) {
	// verify username and password then pass jwt token
    $input = $request->getParsedBody();
    $sql = "SELECT * FROM users WHERE username= :username";
    $sth = $this->db->prepare($sql);
    $sth->bindParam("username", $input['username']);
    $sth->execute();
    $user = $sth->fetchObject();
 
    if(!$user) {
        return $this->response->withJson(['error' => true, 'message' => 'We couldn`t match the username with our records'],401);  
    }
    if (!password_verify($input['password'],$user->password)) {
        return $this->response->withJson(['error' => true, 'message' => 'The password do not match our records.'],401);  
    }
    $settings = $this->get('settings');
    $token = JWT::encode(['username' => $user->username], $settings['jwt']['secret'], "HS256");
    return $this->response->withJson(['token' => $token]);
 
});
