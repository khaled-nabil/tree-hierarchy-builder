<?php

namespace Personia\Tree;

/**
 * Build a tree from an assoc array of node => parent.
 * Provide a breadth-first traversal iterator.
 *
 * Use:
 *     foreach ($tree as $child => $parent)
 *
 * @package Personia\Tree
 */
class Tree implements \IteratorAggregate, \JsonSerializable
{
    /**
     * @var array
     */
    protected $tree = null;

    public function __construct(array $list)
    {
        $this->tree = self::parseList($list);
    }
	//{ "Pete": "Nick", "Barbara": "Nick", "Nick": "Sophie", "Sophie": "Jonas" }
	private function getChildren($name, $list, &$children) {
		if(isset($children[$name])){
			return $children[$name];
		} else {
			foreach($list as $child => $parent) {
				if($name == $parent)
					$children[$name][$child] = $this->getChildren($child,$list,$children);
			}
			if(isset($children[$name]))
				return $children[$name];
			else
				return array();
		}
	}
    /**
     * Transform list-like assoc array [$child => $parent, $grandchild => $child]
     * into hierarchical (tree-like) assoc array [$parent => [$child => [$grandchild => []]
     *
     * @param array $list an assoc array with child as key, parent as value
     * @throws \InvalidArgumentException the input array was malformed
     * @throws RecursionException the input array was correctly formed but contains logic errors (e.g. loops)
     * @return array
     */
    public function parseList(array $list) : array
    {
		if(!$list) 
			throw new \InvalidArgumentException("Invalid input (cannot be decoded)");
		$children = array();
		$tree = array();
		foreach($list as $child => $parent) {
			if(!$child || is_object($child)) throw new \InvalidArgumentException("Child value unassigned for Parent: ".$parent);
			if(!$parent || is_object($parent)) throw new \InvalidArgumentException("Parent value unassigned for Child: ".$child);
			if($parent == $child) throw new RecursionException("Parent and Child must be different values for ".$parent);
			if(@$list[$parent] == $child) throw new RecursionException("Recursion between ".$parent." and ".$child." nodes");
			if(!isset($list[$parent])) { // Start from roots down, if more than 1 root, print error.
				if(sizeof($tree)> 0 && key($tree) != $parent) {
					throw new RecursionException("Tree structure can only have 1 root, roots found: ".key($tree)." and ".$parent);
				}
				$tree[$parent] = $this->getChildren($parent, $list, $children);
			}
		}
		if(sizeof($tree) == 0 && sizeof($list)>0) { // no root found with valid data passed, caused by a loop
			throw new RecursionException("You have a loop in your structure, structure must have a root node");
		}
		return $tree;
    }

    public function getArray(): array
    {
        return $this->tree;
    }

    /**
     * @see \JsonSerializable
     */
    public function jsonSerialize()
    {
        return $this->getArray();
    }

    /**
     * Breadth-first traversal
     *
     * @see \IteratorAggregate
     * @return \Generator|\Traversable
     */
    public function getIterator()
    {
        foreach ($this->tree as $parent => $children) {
            yield $parent => null;
            yield from $this->traverse($parent, $children);
        }
    }

    /**
     * Breadth-first traversal
     *
     * @param string $parent
     * @param array $children
     * @return \Generator
     */
    private function traverse(string $parent, array $children): \Generator
    {
        if (empty($children)) {
            return;
        }

        foreach ($children as $child => $grandChildren) {
            yield $child => $parent;
            yield from $this->traverse($child, $grandChildren);
        }
    }
}