<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTreeStrategy;

use Sagrada\Ai\Strategies\MonteCarloTreeStrategy\Tree\Node;

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
