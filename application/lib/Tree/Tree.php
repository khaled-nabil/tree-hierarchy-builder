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

    /**
     * Transform list-like assoc array [$child => $parent, $grandchild => $child]
     * into hierarchical (tree-like) assoc array [$parent => [$child => [$grandchild => []]
     *
     * @param array $list an assoc array with child as key, parent as value
     * @throws \InvalidArgumentException the input array was malformed
     * @throws RecursionException the input array was correctly formed but contains logic errors (e.g. loops)
     * @return array
     */
    static public function parseList(array $list): array
    {
        // todo

        return [];
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