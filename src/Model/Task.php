<?php

namespace Kkdshka\TodoList\Model;

/**
 * Holds task data.
 *
 * @author Ксю
 */
class Task {
    
    /**
     *
     * @var int 
     */
    private $id;
    
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
    
    /**
     * Return subject's id.
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }
    
    /**
     * Set subject's id.
     * @param int $id
     * @throws \BadMethodCallException When task already has id.
     */
    public function setId(int $id) {
        if (isset($this->id)) {
            throw new \BadMethodCallException("Id had been already set.");
        }
        $this->id = $id;
    }
    
    /**
     * Return true if task has id.
     * @return bool
     */
    public function hasId() : bool {
        return isset($this->id);
    }
}
