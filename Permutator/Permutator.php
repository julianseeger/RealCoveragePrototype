<?php

namespace Permutator;

use Permutator\Line\CoveredLine;
use Permutator\Line\ExecutableLine;
use PHP_CodeCoverage;

class Permutator {
    private $cwd;
    private $baseCommand;

    public function run($coverageFile, $baseCommand, $cwd)
    {
        $this->cwd = $cwd;
        $this->baseCommand = $baseCommand;

        /** @var PHP_CodeCoverage $coverage */
        $coverage = include($coverageFile);

        $coverageData = $coverage->getData();
        foreach ($coverageData as $file => $coveredLines) {
            /** @var CoveredClass $class */
            $class = $this->permutateFile($file, $coveredLines);
            $coverageData[$file] = $class->generateCoveredLinesArray();
        }
        $coverage->setData($coverageData);

        //$coverageGenerator = new \PHP_CodeCoverage_Report_HTML();
        //$coverageGenerator->process($coverage, 'html');
    }

    private function permutateFile($file, $coveredLines)
    {
        echo "Permutating file " . $file . "\n";

        $class = new CoveredClass($file, $coveredLines);
        foreach ($class->getCoveredLines() as $lineNumber => $line) {
            $this->testLine($class, $line);
        }

        $class->reset();

        $this->printResultFile($class);
        return $class;
    }

    private function testLine(CoveredClass $class, CoveredLine $line)
    {
        $line->setCommentedOut(true);
        $this->testPreviousLinesInDependency($class, $line);
        if ($line->isNeccessary()) {
            $class->writeToFile();
            $this->testNeccessityOfLineAtCurrentState($line);
        }

        if ($line->isNeccessary()) {
            $line->setCommentedOut(false);
            $this->testPreviousLinesInDependency($class, $line);
        }
    }

    private function runTest($coveringTest)
    {
        $command = "cd " . $this->cwd . " && ";
        $command .= $this->baseCommand . " --filter ^" . $coveringTest . "$" . " test";
        exec($command, $output, $return);
        return $return === 0;
    }

    /**
     * @param CoveredLine $line
     */
    private function testNeccessityOfLineAtCurrentState(CoveredLine $line)
    {
        foreach ($line->getTests() as $test) {
            $result = $this->runTest($test);
            if ($result) {
                $line->setNeccessary(false);
                break;
            }
        }
    }

    /**
     * @param CoveredClass $class
     * @param CoveredLine $line
     */
    private function testPreviousLinesInDependency(CoveredClass $class, CoveredLine $line)
    {
        try {
            //the following commented out lines may speed it up but will make it very broad
            if (!($line instanceof CoveredLine))
               die('tested an uncovered line? omg');

            $line1 = $class->getPreviousCoveredLine($line->getLineNumber());
            if ($line1 instanceof CoveredLine)
                $this->testLine($class, $line1);
        } catch (\Exception $e) {
        }
    }

    /**
     * @param $class
     */
    private function printResultFile($class)
    {
        foreach ($class->getLines() as $line) {
            if ($line instanceof ExecutableLine) {
                $line->setCommentedOut(false);
            }
            if ($line instanceof CoveredLine) {
                if (!$line->isNeccessary())
                    echo "//";
                echo "+";
            }
            echo $line->getLine() . "\n";
        }
    }
}