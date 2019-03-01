<?php

namespace Tests\Unit;

use Personia\Tree\Tree;

class TreeTest extends \PHPUnit_Framework_TestCase
{
    public function validTrees()
    {
        return [
            'Normal tree' => [['A' => 'B', 'B' => 'C'], ['C' => ['B' => ['A' => []]]]],
            'Deep tree' => [
                [
                    'A' => 'B',
                    'B' => 'C',
                    'C' => 'D',
                    'D' => 'E',
                    'E' => 'F'
                ],
                [
                    'F' => ['E' => ['D' => ['C' => ['B' => ['A' => []]]]]]
                ]
            ],
            'Multi-branch tree' => [
                [
                    'F' => 'C',
                    'E' => 'D',
                    'D' => 'B',
                    'C' => 'A',
                    'B' => 'A',
                ],
                [
                    'A' => ['B' => ['D' =>['E' => []]], 'C' => ['F' => []]]
                ]
            ],
            'Unsorted multi-branch tree' => [
                [
                    'B' => 'A',
                    'D' => 'B',
                    'F' => 'C',
                    'E' => 'D',
                    'C' => 'A',
                ],
                [
                    'A' => ['B' => ['D' =>['E' => []]], 'C' => ['F' => []]]
                ]
            ]
        ];
    }

    /**
     * @dataProvider validTrees
     */
    public function testValidTrees($input, $output)
    {
        $tree = new Tree($input);
        $this->assertEquals($output, $tree->getArray());
    }

    public function brokenTrees()
    {
        return [
            'Empty tree' => [[], '\InvalidArgumentException'],
            'Empty supervisor (null)' => [['A' => null], '\InvalidArgumentException'],
            'Empty supervisor (string)' => [['A' => ''], '\InvalidArgumentException'],
            'Invalid supervisor' => [['A' => new \stdClass()], '\InvalidArgumentException'],
            'Same supervisor' => [['A' => 'A'], 'Personia\Tree\RecursionException'],
            'Same supervisor in a separate tree' => [['A' => 'A', 'B' => 'C'], 'Personia\Tree\RecursionException'],
            '2 steps recursion' => [['A' => 'B', 'B' => 'A'], 'Personia\Tree\RecursionException'],
            '2 steps recursion in a separate tree' => [
                ['A' => 'B', 'B' => 'A', 'C' => 'D'],
                'Personia\Tree\RecursionException'
            ],
            '2 steps recursion + branch' => [['A' => 'B', 'B' => 'A', 'C' => 'A'], 'Personia\Tree\RecursionException'],
            '3 steps recursion' => [['A' => 'B', 'B' => 'C', 'C' => 'A'], 'Personia\Tree\RecursionException'],
            '3 steps recursion + branch' => [
                ['A' => 'B', 'B' => 'C', 'C' => 'A', 'D' => 'A'],
                'Personia\Tree\RecursionException'
            ],
            'Recursion in an unsorted tree' => [['C' => 'A', 'A' => 'B', 'B' => 'B'], 'Personia\Tree\RecursionException']
        ];
    }

    /**
     * @dataProvider brokenTrees
     */
    public function testBrokenTrees($input, $exception)
    {
        $this->expectException($exception);
        new Tree($input);
    }

}