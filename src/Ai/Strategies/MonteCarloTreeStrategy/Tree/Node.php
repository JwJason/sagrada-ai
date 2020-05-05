<?php
declare(strict_types=1);

namespace Sagrada\Ai\Strategies\MonteCarloTreeStrategy\Tree;

class Node
{
    /** @var int */
    protected $aggregateScore;

    /** @var Node[] */
    protected $children;

    /** @var int */
    protected $depth;

    /** @var Node|null */
    protected $parent;

    /** @var bool */
    protected $hasBeenPruned;

    /** @var @int */
    protected $visitCount;

    public function __construct()
    {
        $this->children = [];
        $this->parent = null;
        $this->aggregateScore = 0;
        $this->depth = 0;
        $this->visitCount = 0;
        $this->hasBeenPruned = false;
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
        $this->depth = $this->parent->getDepth() + 1;
    }

    /**
     * @return Node
     */
    public function getRoot(): Node
    {
        $newParent = $this;
        $parent = $this;

        while ($newParent !== null) {
            $newParent = $parent->getParent();
        }

        return $parent;
    }

    /**
     * @return Node[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param Node[] $children
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    /**
     * @param Node $node
     */
    public function addChild(Node $node): void
    {
        $node->setParent($this);
        $this->children[] = $node;
    }

    /**
     * @return Node
     * @throws \Exception
     */
    public function getRandomNode(): Node
    {
        $children = $this->getChildren();

        if (count($children) === 0) {
            throw new \Exception('This node has no child nodes');
        }

        return $children(array_rand($children));
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return count($this->getChildren()) > 0;
    }

    /**
     * @return int
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * @return int
     */
    public function getVisitCount(): int
    {
        return $this->visitCount;
    }

    public function incrementVisitCount(): void
    {
        ++$this->visitCount;
    }

    /**
     * @return int
     */
    public function getAggregateScore(): int
    {
        return $this->aggregateScore;
    }

    /**
     * @param int $amount
     */
    public function increaseAggregateScore(int $amount): void
    {
        $this->aggregateScore += $amount;
    }

    /**
     * @return bool
     */
    public function hasBeenPruned(): bool
    {
        return $this->hasBeenPruned;
    }

    /**
     * @param bool $hasBeenPruned
     */
    public function setHasBeenPruned(bool $hasBeenPruned): void
    {
        $this->hasBeenPruned = $hasBeenPruned;
    }
}
