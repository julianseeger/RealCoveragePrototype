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

        $coverageGenerator = new \PHP_CodeCoverage_Report_HTML();
        $coverageGenerator->process($coverage, 'html');
    }

    private function permutateFile($file, $coveredLines)
    {
        echo "Permutating file " . $file . "\n";

        $class = new CoveredClass($file, $coveredLines);
        foreach ($class->getCoveredLines() as $lineNumber => $line) {
            echo $lineNumber . " => " . is_null($line) . "\n";
            $this->testLine($class, $line);
        }

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
        return $class;
    }

    private function testLine(CoveredClass $class, CoveredLine $line)
    {
        $line->setCommentedOut(true);
        $class->writeToFile();
        if ($line->isNeccessary()) {
            $this->testNeccessityOfLineAtCurrentState($line);
        }
        $this->testPreviousLinesInDependency($class, $line);

        if ($line->isNeccessary()) {
            $line->setCommentedOut(false);
            $class->writeToFile();
        }
    }

    private function runTest($coveringTest)
    {
        $command = "cd " . $this->cwd . " && ";
        $command .= $this->baseCommand . " --filter ^" . $coveringTest . "$" . " test";
        exec($command, $output, $return);
        return $return === 0;
    }

    private function getLine($file, $line)
    {
        $offset = $line-1;
        $content = file_get_contents($file);
        $lines = explode("\n", $content);
        return $lines[$offset];
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
            if ($line->isNeccessary())
                return;

            if (!($line instanceof CoveredLine))
               return;
            $line1 = $class->getPreviousCoveredLine($line->getLineNumber());
            if ($line1 instanceof CoveredLine)
                $this->testLine($class, $line1);
        } catch (\Exception $e) {
        }
    }
}