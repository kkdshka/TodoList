<?php

declare (strict_types = 1);

namespace Kkdshka\TodoList\Repository;

use Kkdshka\TodoList\Model\Task;
use Kkdshka\TodoList\Repository\Repository;
use InvalidArgumentException;
use PDO;
use Exception;

/**
 * Sqlite repository.
 * @author Ксю
 */
class SqliteRepository implements Repository {

    /**
     * PDO object.
     * @var PDO
     */
    private $pdo;

    /**
     * @param string $connectionUrl Path to storage.
     */
    public function __construct(string $connectionUrl) {
        $this->pdo = new PDO($connectionUrl);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = "CREATE TABLE IF NOT EXISTS tasks("
                . "id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "
                . "subject VARCHAR NOT NULL, "
                . "description TEXT, "
                . "status VARCHAR NOT NULL, "
                . "priority INTEGER NOT NULL)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
    }

    /**
     * {@inheritDoc}
     */
    public function create(Task $task) {
        $this->inTransaction(function() use ($task) {
            $stmt = $this->pdo->prepare("INSERT INTO tasks (subject, description, priority, status) VALUES (:subject, :description, :priority, :status)");
            $stmt->execute($this->toStmtParams($task));
            $task->setId((int) $this->pdo->lastInsertId());
        });
    }

    /**
     * {@inheritDoc}
     */
    public function update(Task $task) {
        $this->assertExists($task);
        $stmt = $this->pdo->prepare("UPDATE tasks SET subject = :subject, description = :description, priority = :priority, status = :status WHERE id = :id");
        $stmt->execute($this->toStmtParams($task));
    }

    /**
     * {@inheritDoc}
     */
    public function delete(Task $task) {
        $this->assertExists($task);
        $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $task->getId()]);
    }

    /**
     * {@inheritDoc}
     */
    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM tasks");
        $tasksData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, "toTask"], $tasksData);
    }

    /**
     * {@inheritDoc}
     */
    public function findTaskById(int $id): Task {
        $stmt = $this->pdo->query("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $taskData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($taskData)) {
            throw new NotFoundException("Can't find task with id = $id");
        } else {
            return array_map([$this, "toTask"], $taskData)[0];
        }
    }

    /**
     * Close PDO.
     */
    public function close() {
        $this->pdo = null;
    }

    /**
     * Executes several actions in one transaction.
     * @param callable $block Actions to execute.
     * @throws Exception If error occured while executing block.
     */
    private function inTransaction(callable $block) {
        $this->pdo->beginTransaction();

        try {
            $block();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        $this->pdo->commit();
    }

    /**
     * Ensures if task exicts in repository.
     * @throws InvalidArgumentException When task doesn't have id.
     * @throws NotFoundException When task isn't in repository.
     */
    private function assertExists(Task $task) {
        if (!$task->hasId()) {
            throw new InvalidArgumentException();
        }
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE id = :id LIMIT 1");
        $stmt->execute([$task->getId()]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($data)) {
            throw new NotFoundException();
        }
    }

    /**
     * Extracts data from task and returns it as array.
     * @param Task $task Task object.
     * @return array Task data.
     */
    private function toStmtParams(Task $task): array {
        $params = [
            'subject' => $task->getSubject(),
            'description' => $task->getDescription(),
            'priority' => $task->getPriority(),
            'status' => $task->getStatus()
        ];
        if ($task->hasId()) {
            $params['id'] = $task->getId();
        }
        return $params;
    }

    /**
     * Return task from task data.
     * @param array $taskData Task data.
     * @return Task object.
     */
    private function toTask(array $taskData): Task {
        $task = new Task($taskData['subject'], $taskData['description'], (int) $taskData['priority'], $taskData['status']);
        $task->setId((int) $taskData['id']);
        return $task;
    }
}
