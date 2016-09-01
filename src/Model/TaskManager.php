<?php

namespace Kkdshka\TodoList\Model;

use Kkdshka\TodoList\ {
    Repository\Repository,
    Model\Task
};

/**
 * Manages tasks.
 *
 * @author Ксю
 */
class TaskManager {
    
    /**
     * Task repository.
     * 
     * @var Repository
     */
    private $repository;
    
    /**
     * @param Repository $repository Task repository.
     */
    public function __construct(Repository $repository) {
        $this->repository = $repository;
    }

    /**
     * Creates new task with given subject.
     *  
     * @param string $subject Task's subject.
     */
    public function create(string $subject) {
        $task = new Task($subject);
        $this->repository->create($task);
    }
    
    /**
     * Completes given task.
     * 
     * @param Task $task Task to complete.
     */
    public function complete(Task $task) {
        $task->complete();
        $this->repository->update($task);
    }
    
    /**
     * Deletes given task.
     * 
     * @param Task $task Task to delete.
     */
    public function delete(Task $task) {
        $this->repository->delete($task);
    }
    
    /**
     * @return Tasks[] All tasks.
     */
    public function getAll() : array {
        return $this->repository->getAll();
    }
}
