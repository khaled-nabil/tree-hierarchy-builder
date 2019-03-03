<?php

namespace Personia\Controllers;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class GetHierarchyController
{
    /**
     * @var \PDO
     */
    private $dbConnection;

    public function __construct(Container $container)
    {
        $this->dbConnection = $container->get('db');
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        try {
			if(!$args['name']) // route whouldn't really get here if no argument provided, but just for future purposes (if any)
				throw new \InvalidArgumentException('No employee selected');
			$supervisors = array();
			$next = $args['name'];
			$found = false;
			while($next) {
				$result = $this->fetchSupervisor($next);
				$next = $result;
				if($result === null) //in case the parent is null, item is found but no parent.
					$found = true;
				if($result)
					$supervisors[] = $result;
			}
			if(sizeof($supervisors)>0)
				return $response->withJson($supervisors,200,JSON_PRETTY_PRINT);
			else
				if($found)
					return $response->withJson($supervisors,204);
				else
					return $response->withJson(['error' => "no resource found"],404);
        } catch (\InvalidArgumentException $e) {
            return $response->withJson(['error' => $e->getMessage()], 400);
        } catch (\Error $e) {
			return $response->withJson(['error' => $e->getMessage()], 400);
		}
    }
	private function fetchSupervisor(String $employee) {
		$sql = sprintf("SELECT supervisor FROM employees WHERE name = '%s' ",$employee);
		$query = $this->dbConnection->prepare($sql);
		$query->execute();
		$result = $query->fetchObject();
		if($result) {
			return $result->supervisor;
		}
		return false;
	}
}
