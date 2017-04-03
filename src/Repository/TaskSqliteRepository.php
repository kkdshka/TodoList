<?php
declare (strict_types = 1);

namespace Kkdshka\TodoList\Repository;

use Kkdshka\TodoList\Model\{
    Task,
    User
};
use InvalidArgumentException;
use PDO;
use Exception;

/**
 * Sqlite repository for tasks.
 * 
 * @author kkdshka
 */
class TaskSqliteRepository {

    /**
     * PDO object.
     * 
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
            . "priority INTEGER NOT NULL, "
            . "user_id INTEGER NOT NULL)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
    }

    
    /**
     * Saves new task in db.
     * 
     * @param Task $task Task to save.
     */
    public function create(Task $task) {
        $this->inTransaction(function() use ($task) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO tasks (subject, description, priority, status, user_id) "
                . "VALUES (:subject, :description, :priority, :status, :user_id)"
            );
            $stmt->execute($this->toStmtParams($task));
            $task->setId((int) $this->pdo->lastInsertId());
        });
    }

    /**
     * Updates task.
     * 
     * @param Task $task Task to update.
     */
    public function update(Task $task) {
        // :TODO: wrap into transaction
        $this->assertExists($task);
        $stmt = $this->pdo->prepare(
            "UPDATE tasks SET "
            . "subject = :subject, "
            . "description = :description, "
            . "priority = :priority, "
            . "status = :status,"
            . "user_id = :user_id "
            . "WHERE id = :id");
        $stmt->execute($this->toStmtParams($task));
    }

    /**
     * Deletes task from db.
     * 
     * @param Task $task Task to delete.
     */
    public function delete(Task $task) {
        // :TODO: wrap into transaction
        $this->assertExists($task);
        $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $task->getId()]);
    }

    /**
     * Finds task by id.
     * 
     * @param int $id Task id.
     * @param User $author Task author.
     * @return Task
     * @throws NotFoundException When task with given id doesn't exist.
     */
    public function find(int $id, User $author) : Task {
        $stmt = $this->pdo->query("SELECT * FROM tasks WHERE id = :id AND user_id = :user_id LIMIT 1");
        $stmt->execute(['id' => $id, 'user_id' => $author->getId()]);
        $taskData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($taskData)) {
            throw new NotFoundException("Can't find task with id = $id");
        } 
        return $this->toTask($taskData[0], $author);
    }

    /**
     * Returns all user's tasks.
     * 
     * @param User $author Tasks author.
     * @return array Tasks.
     * @throws NotFoundException When no tasks for given user were found.
     */
    public function getUserTasks(User $author) : array {
        $query = "SELECT * FROM tasks WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['user_id' => $author->getId()]);
        $taskData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($taskData)) {
            throw new NotFoundException("Can't find tasks for user {$author->getLogin()}.");
        } 
        return array_map(function ($taskData) use ($author)  {
            return $this->toTask($taskData, $author);
        }, $taskData);
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
     * 
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
     * 
     * @param Task $task Task object.
     * @return array Task data.
     */
    private function toStmtParams(Task $task) : array {
        $user = $task->getUser();
        $params = [
            'subject' => $task->getSubject(),
            'description' => $task->getDescription(),
            'priority' => $task->getPriority(),
            'status' => $task->getStatus(),
            'user_id' => $user->getId()
        ];
        if ($task->hasId()) {
            $params['id'] = $task->getId();
        }
        return $params;
    }

    /**
     * Return task from task data.
     * 
     * @param array $taskData Task data.
     * @return Task object.
     */
    private function toTask(array $taskData, User $user) : Task {
        $task = new Task(
            $user,
            $taskData['subject'], 
            $taskData['description'], 
            (int) $taskData['priority'], 
            $taskData['status']
        );
        $task->setId((int) $taskData['id']);
        return $task;
    }
    
}
