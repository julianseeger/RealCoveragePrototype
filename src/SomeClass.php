<?php
/**
 * Created by PhpStorm.
 * User: julian
 * Date: 19.02.14
 * Time: 19:21
 */

class SomeClass {
    public function coverMe()
    {
        $result = "123";
        $a = true;
        if (!$a)
            $result = false;

        $this->alsoCovered();
        $c = "d";
        return $result;
    }

    public function alsoCovered()
    {
        $do = "something";
    }

    public function definitelyNotCovered()
    {
        $so = "wayne";
    }
}
