<?php
declare(strict_types=1);

namespace Sagrada\Turn;

use Sagrada\Turn;

class DiePlacement extends Turn
{
    /** @var \Sagrada\DiePlacement */
    protected $diePlacement;

    public function __construct(\Sagrada\DiePlacement $diePlacement)
    {
        $this->diePlacement = $diePlacement;
    }

    /**
     * @return \Sagrada\DiePlacement
     */
    public function getDiePlacement(): \Sagrada\DiePlacement
    {
        return $this->diePlacement;
    }

    public function __toString()
    {
        return (string)$this->getDiePlacement();
    }
}
