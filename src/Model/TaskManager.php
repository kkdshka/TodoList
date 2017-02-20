<?php

declare (strict_types = 1);

namespace Kkdshka\TodoList\Model;

use Kkdshka\TodoList\{
    Repository\Repository,
    Model\Task
};

/**
 * Manages tasks.
 * @author Ксю
 */
class TaskManager {

    /**
     * Task repository.
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
     * @param Task $task Task to delete.
     */
    public function delete(Task $task) {
        $this->repository->delete($task);
    }

    /**
     * @return Tasks[] All tasks.
     */
    public function getAll(): array {
        return $this->repository->getAll();
    }

    /**
     * Returns task with given id.
     * @param int $id Given id.
     * @return Task Task with given id.
     */
    public function findTaskById(int $id): Task {
        return $this->repository->findTaskById($id);
    }
}
