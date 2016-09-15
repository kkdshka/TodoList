<?php

declare (strict_types = 1);

namespace Kkdshka\TodoList\Repository;

use Kkdshka\TodoList\Model\Task;
use Kkdshka\TodoList\Repository\Repository;
use RuntimeException;
use InvalidArgumentException;

/**
 * Description of CsvRepository
 *
 * @author Ксю
 */
class CsvRepository implements Repository {
    
    /**
     * Contains next new id.
     * @var int 
     */
    private $nextId;
    
    /**
     * Path to file where tasks will be saved.
     * @var string 
     */
    private $tasksStoragePath;
    
    /**
     * Contains all tasks in array, where key is id of task.
     * ['task id' => 'task object']
     * @var array
     */
    private $tasks;
    
    /**
     * Resource for starage file.
     * @var resource
     */
    private $storageHandle;
    
    /**
     * Lock file or not.
     * @var bool 
     */
    private $lockStorageFile;
    
    /**
     * @param string $tasksStoragePath Path to storage file.
     * @param bool $lockStorageFile Flag should lock file.
     * @throws RuntimeException If can't open or lock storage file.
     */
    public function __construct(string $tasksStoragePath, bool $lockStorageFile = true) {
        $this->tasksStoragePath = $tasksStoragePath;
        $this->lockStorageFile = $lockStorageFile;
        if (!file_exists($this->tasksStoragePath)) {
            throw new RuntimeException("File with path {$this->tasksStoragePath} doesn't exist.");
        }
        $this->storageHandle = fopen($this->tasksStoragePath, 'c+');
        if (!$this->storageHandle) {
            throw new RuntimeException("Can't open file {$this->tasksStoragePath}.");
        }
        if ($this->lockStorageFile) {
            if (!flock($this->storageHandle, LOCK_EX)) {
                throw new RuntimeException("Can't lock file {$this->tasksStoragePath}.");
            }
        }
        $this->tasks = $this->getTasksFromCsv();
        $this->nextId = $this->determineNextId();
    }
    
    /**
     * {@inheritDoc}
     */
    public function delete(Task $task) {
        $this->assertExists($task);
        unset($this->tasks[$task->getId()]);
        $this->saveTasksToCsv();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getAll(): array {
        return array_values($this->tasks);
    }

    /**
     * {@inheritDoc}
     */
    public function create(Task $task) {
        $task->setId($this->nextId);
        $this->tasks[$this->nextId] = $task;
        $this->saveTasksToCsv();
        $this->nextId++;
    }
    
    /**
     * {@inheritDoc}
     */
    public function update(Task $task) {
        $this->assertExists($task);
        $this->tasks[$task->getId()] = $task;
        $this->saveTasksToCsv();
    }
    
    /**
     * {@inheritDoc}
     */
    public function close() {
        if ($this->lockStorageFile) {
            if (!flock($this->storageHandle, LOCK_UN)) {
                throw new RuntimeException("Can't unlock file {$this->tasksStoragePath}.");
            }
        }
        if (!fclose($this->storageHandle)) {
            throw new RuntimeException("Can't close file {$this->tasksStoragePath}.");
        }
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
        if (!array_key_exists($task->getId(), $this->tasks)) {
            throw new NotFoundException();
        }
    }
            
    /**
     * Makes array of tasks from csv file.
     * @return array All tasks.
     */
    private function getTasksFromCsv() : array {
        rewind($this->storageHandle);
        $tasks = [];
        while (($taskData = fgetcsv($this->storageHandle))) {
            $task = $this->toTask($taskData);
            $tasks[$task->getId()] = $task;
        }
        return $tasks;
    }

    /**
     * Saves tasks to csv file.
     */
    private function saveTasksToCsv() {
        rewind($this->storageHandle);
        ftruncate($this->storageHandle, 0);
        foreach ($this->tasks as $task) {
            fputcsv($this->storageHandle, $this->toArray($task));
        }
    }
    
    /**
     * Makes array of tasks from tasks data.
     * @param array $tasksData
     * @return Task
     */
    private function toTask(array $tasksData) : Task {
        list($id, $subject, $isCompleted) = $tasksData;
        $task = new Task($subject);
        $task->setId($id);
        if ($isCompleted) {
            $task->complete();
        }
        return $task;
    }
    
    /**
     * Make task data from object task.
     * @param Task $task
     * @return array Task data
     */
    private function toArray(Task $task) : array {
        return [$task->getId(), $task->getSubject(), $task->isCompleted()];
    }
    
    /**
     * Return next id.
     * @return int Next id.
     */
    private function determineNextId() : int {
        if (empty($this->tasks)) {
            return 1;
        }
        return max(array_keys($this->tasks)) + 1;
    }
}
