<?php

namespace Kkdshka\TodoList\Model;

/**
 * Holds task data.
 *
 * @author Ксю
 */
class Task {
    
    /**
     * Task's subject.
     * 
     * @var string
     */
    private $subject;
    
    /**
     * Flag, is task completed or not.
     * 
     * @var bool 
     */
    private $isCompleted;
    
    /**
     * @param string $subject Task's subject.
     */
    public function __construct(string $subject) {
        $this->subject = $subject;
        $this->isCompleted = false;
    }
    
    /**
     * @return string Task's subject.
     */
    public function getSubject() : string {
        return $this->subject;
    }
    
    /**
     * Completes task.
     */
    public function complete() {
        $this->isCompleted = true;
    }
    
    /**
     * @return bool True if task is completed.
     */
    public function isCompleted() : bool {
        return $this->isCompleted;
    }
}
