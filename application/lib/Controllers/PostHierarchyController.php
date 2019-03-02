<?php

namespace Personia\Controllers;

use Personia\Tree\Tree;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class PostHierarchyController
{
    /**
     * @var \PDO
     */
    private $dbConnection;

    public function __construct(Container $container)
    {
        $this->dbConnection = $container->get('db');
    }

    public function __invoke(Request $request, Response $response)
    {
        // https://github.com/php-fig/http-message/blob/master/src/ServerRequestInterface.php#L167
        $data = $request->getParsedBody();
        if (!$data) {
            return $response->withJson(['error' => 'Invalid input (cannot be decoded)'], 400);
        }

        try {
            $tree = new Tree($data);
			return $response->withJson($tree, 200);

            $this->dbConnection->beginTransaction();
            try {
                $this->dbConnection->exec('DELETE FROM employees');

                foreach ($tree as $child => $parent) {
                    $sql = sprintf(
                        "INSERT INTO employees (name, supervisor) VALUES ('%s','%s') ",
                        $child,
                        $parent
                    );

                    $this->dbConnection->exec($sql);
                }

                $this->dbConnection->commit();
            } catch (\PDOException $exception) {
                $this->dbConnection->rollBack();
                throw $exception;
            }

            return $response->withJson($tree, 200, JSON_PRETTY_PRINT);
        } catch (\InvalidArgumentException $e) {
            return $response->withJson(['error' => $e->getMessage()], 422);
        }
    }
}
