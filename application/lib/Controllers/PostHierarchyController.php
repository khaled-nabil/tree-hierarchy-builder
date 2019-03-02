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
        try {
			$data = $request->getParsedBody();
			if(!$data)
				throw new \InvalidArgumentException("Invalid input (cannot be decoded)");
            $tree = new Tree($data);
            $this->dbConnection->beginTransaction();
			$this->dbConnection->exec('DELETE FROM employees');
			foreach ($tree as $child => $parent) {
				$sql = sprintf("INSERT INTO employees (name, supervisor) VALUES ('%s','%s') ",
					$child,
					$parent
				);
				$this->dbConnection->exec($sql);
			}
			$this->dbConnection->commit();

            return $response->withJson($tree, 200, JSON_PRETTY_PRINT);
        } catch (\PDOException $exception) {
			$this->dbConnection->rollBack();
			return $response->withJson(['error' => $e->getMessage()], 500);
		} catch(\Personia\Tree\RecursionException $e) {
			return $response->withJson(['error' => $e->getMessage()], 422);
		} catch (\InvalidArgumentException $e) {
            return $response->withJson(['error' => $e->getMessage()], 400);
        }catch (\TypeError $e) {
			return $response->withJson(['error' => $e->getMessage()], 400);
		}
    }
}
