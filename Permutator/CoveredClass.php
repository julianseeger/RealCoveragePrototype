<?php
/**
 * Created by PhpStorm.
 * User: julian
 * Date: 19.02.14
 * Time: 20:44
 */

namespace Permutator;


use Permutator\Line\CoveredLine;
use Permutator\Line\ExecutableLine;
use Permutator\Line\NotExecutableLine;

class CoveredClass implements Observer {
    /**
     * @var CoveredLine[]
     */
    private $coveredLines;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var array
     */
    private $lines = array();

    /**
     * @param string $fileName
     * @param $coveredLines
     */
    function __construct($fileName, $coveredLines)
    {
        $this->fileName = $fileName;
        $this->coveredLines = array();

        $this->readContent($fileName, $coveredLines);
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    private function readContent($fileName, $coveredLines)
    {
        $content = file_get_contents($fileName);
        $lines = explode("\n", $content);

        foreach ($lines as $index => $line) {
            $lineNumber = $index+1;
            $this->lines[$lineNumber] = $this->parseLine($lineNumber, $coveredLines, $line);

        }
    }

    /**
     * @param int $lineNumber
     * @param string[] $coverage
     * @param string $line
     * @return CoveredLine|ExecutableLine|NotExecutableLine
     */
    private function parseLine($lineNumber, $coverage, $line)
    {
        if (!isset($coverage[$lineNumber]) || is_null($coverage[$lineNumber]))
            return new NotExecutableLine($line, $lineNumber);

        return $this->createCoveredLine($lineNumber, $coverage[$lineNumber], $line);
    }

    public function getPreviousCoveredLine($lineNumber)
    {
        $previousLine = null;
        foreach ($this->coveredLines as $number => $line) {
            if ($lineNumber == $number)
                return $previousLine;

            $previousLine = $line;
        }

        die("illegal state: requested previous line of " . $lineNumber . " but lines are " . var_export($this->coveredLines, true));
    }

    public function writeToFile()
    {
        file_put_contents($this->fileName, $this->__toString());
    }

    function __toString()
    {
        return join("\n", $this->lines);
    }

    /**
     * @param $lineNumber
     * @param $coverage
     * @param $line
     * @return CoveredLine
     */
    private function createCoveredLine($lineNumber, $coverage, $line)
    {
        $coveredLine = new CoveredLine($line, $lineNumber, $coverage);
        $this->coveredLines[$lineNumber] = $coveredLine;
        $coveredLine->addObserver($this);
        return $coveredLine;
    }

    public function getCoveredLines()
    {
        return $this->coveredLines;
    }

    public function isCovered($lineNumber)
    {
        return isset($this->coveredLines[$lineNumber]);
    }

    public function getLines()
    {
        return $this->lines;
    }

    public function update($observable)
    {
        if (!($observable instanceof CoveredLine))
            return;
    }

    public function generateCoveredLinesArray()
    {
        $result = array();
        foreach ($this->lines as $lineNumber => $line) {
            if (!($line instanceof ExecutableLine))
                continue;

            if ($line instanceof CoveredLine && $line->isNeccessary())
                $result[$lineNumber] = $line->getTests();
            else
                $result[$lineNumber] = null;
        }
        return $result;
    }

    public function reset()
    {
        foreach ($this->coveredLines as $line)
            $line->setCommentedOut(false);
        $this->writeToFile();
    }
}
