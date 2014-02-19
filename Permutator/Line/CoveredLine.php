<?php
/**
 * Created by PhpStorm.
 * User: julian
 * Date: 19.02.14
 * Time: 21:21
 */

namespace Permutator\Line;


class CoveredLine extends ExecutableLine {
    private $neccessary = true;
    private $tests;

    function __construct($line, $lineNumber, $tests)
    {
        parent::__construct($line, $lineNumber);
        $this->tests = $tests;
    }


    public function isNeccessary() {
        return $this->neccessary;
    }

    public function setNeccessary($neccessary) {
        $this->neccessary = $neccessary;
    }

    /**
     * @return mixed
     */
    public function getTests()
    {
        return $this->tests;
    }
}