<?php
/**
 * Created by PhpStorm.
 * User: julian
 * Date: 19.02.14
 * Time: 21:20
 */

namespace Permutator\Line;


use Permutator\Observer;

class ExecutableLine extends AbstractLine {
    private $commentedOut = false;

    /**
     * @var Observer[]
     */
    private $observers = array();

    public function isCommentedOut() {
        return $this->commentedOut;
    }

    public function setCommentedOut($commentedOut) {
        $this->commentedOut = $commentedOut;

        $this->notifyObservers();
    }

    function __toString()
    {
        $comment = $this->commentedOut ? '//' : '';
        return $comment . parent::__toString();
    }

    public function addObserver(Observer $observer) {
        $this->observers[] = $observer;
    }

    private function notifyObservers()
    {
        foreach ($this->observers as $observer) {
            $observer->update();
        }
    }
}