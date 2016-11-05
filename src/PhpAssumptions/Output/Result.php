<?php

namespace PhpAssumptions\Output;

class Result
{
    /**
     * @var array
     */
    private $assumptions = [];

    /**
     * @var int
     */
    private $boolExpressionsCount = 0;

    /**
     * @param string $file
     * @param int    $line
     * @param string $message
     */
    public function addAssumption($file, $line, $message)
    {
        $this->assumptions[] = [
            'file' => $file,
            'line' => $line,
            'message' => $message,
        ];
    }

    public function increaseBoolExpressionsCount()
    {
        $this->boolExpressionsCount++;
    }

    /**
     * @return array
     */
    public function getAssumptions()
    {
        return $this->assumptions;
    }

    /**
     * @return int
     */
    public function getAssumptionsCount()
    {
        return count($this->assumptions);
    }

    /**
     * @return float
     */
    public function getPercentage()
    {
        if ($this->getBoolExpressionsCount() === 0) {
            return 0;
        }

        return round($this->getAssumptionsCount() / $this->getBoolExpressionsCount() * 100);
    }

    /**
     * @return int
     */
    public function getBoolExpressionsCount()
    {
        return $this->boolExpressionsCount;
    }
}
