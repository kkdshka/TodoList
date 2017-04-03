<?php
declare (strict_types = 1);

namespace Kkdshka\TodoList\Model;

use Kkdshka\TodoList\{
    Repository\TaskSqliteRepository,
    Model\Task,
    Model\User
};

/**
 * Manages tasks.
 * 
 * @author kkdshka
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
    public function __construct(TaskSqliteRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Creates new task.
     */
    public function create(Task $task) {
        $this->repository->create($task);
    }

    /**
     * Updates existed task.
     */
    public function update(Task $task) {
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
     * Returns task with given id.
     * 
     * @param int $id Given id.
     * @return Task Task with given id.
     */
    public function find(int $id, User $user) : Task {
        return $this->repository->find($id, $user);
    }
    
    /**
     * Returns all user's tasks.
     * 
     * @param User $user Task author.
     * @return array
     */
    public function getUserTasks(User $user) : array {
        return $this->repository->getUserTasks($user);
    }
    
}
