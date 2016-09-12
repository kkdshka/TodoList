<?php

namespace Kkdshka\TodoList\Repository;

use PHPUnit\Framework\TestCase;
use Kkdshka\TodoList\Model\Task;
use PDO;

/**
 * Description of CsvRepositoryTest
 *
 * @author Ксю
 */
class SqliteRepositoryTest extends TestCase {
    
    private $filename;
    
    private $repository;
    
    public function setUp() {
        $this->filename = tempnam(sys_get_temp_dir(), 'sqlite');
        $this->repository = new SqliteRepository("sqlite:" . $this->filename);
    }
    
    public function tearDown() {
        $this->repository->close();
        unlink($this->filename);
    }
    
    /**
     * @return array Include actual data.
     */
    public function getAll() : array {
        $pdo = new PDO("sqlite:" . $this->filename);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->query("SELECT * FROM tasks");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::create
     */
    public function shouldCreateNewTask() {
        $task = new Task("Test subject");
                
        $this->repository->create($task);
        
        $this->assertEquals([['id' => '1', 'subject' => "Test subject", 'is_completed' => 0]], $this->getAll());
    }
    
    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::update
     */
    public function shouldUpdateExistedTask() {
        $task = new Task("Test subject");
        
        $this->repository->create($task);
        $task->complete();
        $this->repository->update($task);
        
        $this->assertEquals([['id' => '1', 'subject' => "Test subject", 'is_completed' => 1]], $this->getAll());
    }
    
    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::delete
     */
    public function shouldDeleteExistedTask() {
        $task = new Task("Test subject");
        
        $this->repository->create($task);
        $this->repository->delete($task);
        
        $this->assertEquals([], $this->getAll());
    } 
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::getAll
     */
    public function shouldGetAllTasks() {
        $firstTask = new Task("First test subject");
        $secondTask = new Task("Second test subject");
               
        $this->repository->create($firstTask);
        $this->repository->create($secondTask);
        
        $this->assertEquals([$firstTask, $secondTask], $this->repository->getAll());
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::create
     */
    public function shouldCreateTwoNewTasks() {
        $firstTask = new Task("First test subject");
        $secondTask = new Task("Second test subject");
        
        $this->repository->create($firstTask);
        $this->repository->create($secondTask);
        
        $this->assertEquals([['id' => '1', 'subject' => "First test subject", 'is_completed' => 0], 
                             ['id' => '2', 'subject' => "Second test subject", 'is_completed' => 0]], $this->getAll()); 
    }
    
    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::update
     */
    public function shouldNotAffectOtherTasksWhenUpdate() {
        $firstTask = new Task("First test subject");
        $secondTask = new Task("Second test subject");
        
        $this->repository->create($firstTask);
        $this->repository->create($secondTask);
        $firstTask->complete();
        $this->repository->update($firstTask);
        
        $this->assertEquals([['id' => '1', 'subject' => "First test subject", 'is_completed' => 1], 
                             ['id' => '2', 'subject' => "Second test subject", 'is_completed' => 0]], $this->getAll());
    }
    
    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::delete
     */
    public function shouldNotAffectOtherTasksWhenDelete() {
        $firstTask = new Task("First test subject");
        $secondTask = new Task("Second test subject");
        
        $this->repository->create($firstTask);
        $this->repository->create($secondTask);
        $this->repository->delete($firstTask);
        
        $this->assertEquals([['id' => '2', 'subject' => "Second test subject", 'is_completed' => 0]], $this->getAll());
    }
    
    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::getAll
     */
    public function shouldReturnNewTasksWhenGetAll() {
        $firstTask = new Task("First test subject");
        $secondTask = new Task("Second test subject");
        
        $this->repository->create($firstTask);
        $this->assertEquals([$firstTask], $this->repository->getAll());
        
        $this->repository->create($secondTask);
        $this->assertEquals([$firstTask, $secondTask], $this->repository->getAll());
    }
    
    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::getAll
     */
    public function shouldNotReturnDeletedTasksWhenGetAll() {
        $firstTask = new Task("First test subject");
        $secondTask = new Task("Second test subject");
        
        $this->repository->create($firstTask);
        $this->repository->create($secondTask);
        $this->assertEquals([$firstTask, $secondTask], $this->repository->getAll());
        
        $this->repository->delete($secondTask);
        $this->assertEquals([$firstTask], $this->repository->getAll());
    }
    
    /**
     * @test
     * @expectedException Kkdshka\TodoList\Repository\NotFoundException
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::update
     */
    public function shouldNotUpdateNotExistedTask() {
        $task = new Task("Test subject");
        $task->setId(1);
        
        $this->repository->update($task);
    }
    
    /**
     * @test
     * @expectedException InvalidArgumentException
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::update
     */
    public function shouldNotUpdateUnsavedTask() {
        $task = new Task("Test subject");
        
        $this->repository->update($task);
    }
    
    /**
     * @test
     * @expectedException Kkdshka\TodoList\Repository\NotFoundException
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::delete
     */
    public function shouldNotDeleteNotExistedTask() {
        $task = new Task("Test subject");
        $task->setId(1);
        
        $this->repository->delete($task);
    }
    
    /**
     * @test
     * @expectedException InvalidArgumentException
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::delete
     */
    public function shouldNotDeleteUnsavedTask() {
        $task = new Task("Test subject");
        
        $this->repository->delete($task);
    }
    
}