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
        $stmt = $this->pdo->prepare(
                "CREATE TABLE IF NOT EXISTS tasks("
                . "id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "
                . "subject VARCHAR NOT NULL, "
                . "is_completed INTEGER NOT NULL)");
        $stmt->execute();
    }
    
    /**
     * {@inheritDoc}
     */
    public function create(Task $task) {
        $this->inTransaction(function() use ($task) {
            $stmt = $this->pdo->prepare("INSERT INTO tasks (subject, is_completed) VALUES (:subject, :is_completed)");
            $stmt->execute($this->toStmtParams($task));
            $task->setId((int) $this->pdo->lastInsertId());
        });
    }

    
    /**
     * {@inheritDoc}
     */
    public function update(Task $task) {
        $this->assertExists($task);
        $stmt = $this->pdo->prepare("UPDATE tasks SET subject = :subject, is_completed = :is_completed WHERE id = :id");
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
        }
        catch (Exception $e) {
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
    private function toStmtParams(Task $task) : array {
        $isCompleted = 1;
        if (!$task->isCompleted()) {
            $isCompleted = 0;
        }
        if ($task->hasId()) {
           return ['subject' => $task->getSubject(), 'is_completed' => "$isCompleted", 'id' => $task->getId()];
        }
        return ['subject' => $task->getSubject(), 'is_completed' => "$isCompleted"];
    }
    
    /**
     * Return task from task data.
     * @param array $taskData Task data.
     * @return Task object.
     */
    private function toTask(array $taskData) : Task {
        $task = new Task($taskData['subject']);
        $task->setId((int) $taskData['id']);
        if ($taskData['is_completed'] == 1) {
            $task->complete();
        }
        return $task;
    }
}
