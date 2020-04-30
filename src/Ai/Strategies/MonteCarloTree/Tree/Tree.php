<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTree\Tree;

class Tree
{
    /** @var Node */
    protected $rootNode;

    public function __construct(Node $rootNode)
    {
        $this->rootNode = $rootNode;
    }

    /**
     * @return Node
     */
    public function getRootNode(): Node
    {
        return $this->rootNode;
    }
}
