<?php

require_once __DIR__ . '/../vendor/autoload.php';

$permutator = new \Permutator\Permutator();
$permutator->run(__DIR__ . '/../coverage.php', './vendor/bin/phpunit --bootstrap bootstrap.php', __DIR__ . '/..');