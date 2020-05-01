<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTree\Tree;

use Sagrada\Ai\Strategies\MonteCarloTree\NodeData;

class Node
{
    /** @var NodeData|null */
    protected $data;

    /** @var Node|null */
    protected $parent;

    /** @var Node[] */
    protected $childArray;

    public function __construct(?NodeData $data)
    {
        if ($data) {
            $this->setData($data);
        }
        $this->childArray = [];
        $this->parent = null;
    }

    /**
     * @return NodeData
     */
    public function getData(): NodeData
    {
        return $this->data;
    }

    /**
     * @param NodeData $data
     */
    public function setData(NodeData $data): void
    {
        $this->data = $data;
    }

    /**
     * @return Node|null
     */
    public function getParent(): ?Node
    {
        return $this->parent;
    }

    /**
     * @param Node|null $parent
     */
    public function setParent(?Node $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return Node[]
     */
    public function getChildArray(): array
    {
        return $this->childArray;
    }

    /**
     * @param Node[] $childArray
     */
    public function setChildArray(array $childArray): void
    {
        $this->childArray = $childArray;
    }

    /**
     * @param Node $node
     */
    public function addNodeToChildArray(Node $node): void
    {
        $node->setParent($this);
        $this->childArray[] = $node;
    }

    /**
     * @return Node
     * @throws \Exception
     */
    public function getRandomNode(): Node
    {
        $children = $this->getChildArray();

        if (count($children) === 0) {
            throw new \Exception('This node has no children nodes');
        }

        return $children(array_rand($children));
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return count($this->getChildArray()) > 0;
    }
}
