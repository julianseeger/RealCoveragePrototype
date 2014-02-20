<?php
/**
 * Created by PhpStorm.
 * User: julian
 * Date: 19.02.14
 * Time: 22:18
 */

namespace Permutator;


interface Observer {
    public function update($observable);
} 