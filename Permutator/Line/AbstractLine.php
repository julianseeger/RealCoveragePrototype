<?php
/**
 * Created by PhpStorm.
 * User: julian
 * Date: 19.02.14
 * Time: 21:18
 */

namespace Permutator\Line;


abstract class AbstractLine implements Line {
    private $line;
    private $lineNumber;

    function __construct($line, $lineNumber)
    {
        $this->line = $line;
        $this->lineNumber = $lineNumber;
    }

    /**
     * @return mixed
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return mixed
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    function __toString()
    {
        return $this->getLine();
    }
}