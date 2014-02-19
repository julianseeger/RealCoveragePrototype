<?php
/**
 * Created by PhpStorm.
 * User: julian
 * Date: 19.02.14
 * Time: 19:22
 */

class SomeClassTest extends PHPUnit_Framework_TestCase {
    public function testSomeClass()
    {
        $sut = new SomeClass();
        $this->assertEquals("123", $sut->coverMe());
    }
} 