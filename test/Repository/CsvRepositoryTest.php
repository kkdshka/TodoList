<?php

namespace Kkdshka\TodoList\Repository;

use PHPUnit\Framework\TestCase;
use Kkdshka\TodoList\Model\Task;

/**
 * Description of CsvRepositoryTest
 *
 * @author kkdshka
 */
class CsvRepositoryTest extends TestCase {
    
    /**
     * @var string  
     */
    private $filename;
    
    /**
     * @var CsvRepository
     */
    private $repository;
    
    public function setUp() {
        $this->filename = tempnam(sys_get_temp_dir(), 'csv');
        $this->repository = new CsvRepository($this->filename, false);
    }
    
    public function tearDown() {
        $this->repository->close();
        unlink($this->filename);
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\CsvRepository::create
     */
    public function shouldCreateNewTask() {
        $task = new Task("Test subject");
                
        $this->repository->create($task);
        
        $this->assertEquals("1,\"Test subject\",,3,New\n", file_get_contents($this->filename));
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\CsvRepository::update
     */
    public function shouldUpdateExistedTask() {
        $task = new Task("Test subject");
        
        $this->repository->create($task);
        $task->setPriority(5);
        $this->repository->update($task);
        
        $this->assertEquals("1,\"Test subject\",,5,New\n", file_get_contents($this->filename));
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\CsvRepository::delete
     */
    public function shouldDeleteExistedTask() {
        $task = new Task("Test subject");
        
        $this->repository->create($task);
        $this->repository->delete($task);
        
        $this->assertEquals("", file_get_contents($this->filename));
    } 
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\CsvRepository::getAll
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
     * @covers Kkdshka\TodoList\Repository\CsvRepository::create
     */
    public function shouldCreateTwoNewTasks() {
        $firstTask = new Task("First test subject");
        $secondTask = new Task("Second test subject");
        
        $this->repository->create($firstTask);
        $this->repository->create($secondTask);
        
        $this->assertEquals("1,\"First test subject\",,3,New\n2,\"Second test subject\",,3,New\n", file_get_contents($this->filename)); 
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\CsvRepository::update
     */
    public function shouldNotAffectOtherTasksWhenUpdate() {
        $firstTask = new Task("First test subject");
        $secondTask = new Task("Second test subject");
        
        $this->repository->create($firstTask);
        $this->repository->create($secondTask);
        $firstTask->setPriority(5);
        $this->repository->update($firstTask);
        
        $this->assertEquals("1,\"First test subject\",,5,New\n2,\"Second test subject\",,3,New\n", file_get_contents($this->filename));
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\CsvRepository::delete
     */
    public function shouldNotAffectOtherTasksWhenDelete() {
        $firstTask = new Task("First test subject");
        $secondTask = new Task("Second test subject");
        
        $this->repository->create($firstTask);
        $this->repository->create($secondTask);
        $this->repository->delete($firstTask);
        
        $this->assertEquals("2,\"Second test subject\",,3,New\n", file_get_contents($this->filename));
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\CsvRepository::getAll
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
     * @covers Kkdshka\TodoList\Repository\CsvRepository::getAll
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
     * @covers Kkdshka\TodoList\Repository\CsvRepository::update
     */
    public function shouldNotUpdateNotExistedTask() {
        $task = new Task("Test subject");
        $task->setId(1);
        
        $this->repository->update($task);
    }
    
    /**
     * @test
     * @expectedException InvalidArgumentException
     * @covers Kkdshka\TodoList\Repository\CsvRepository::update
     */
    public function shouldNotUpdateUnsavedTask() {
        $task = new Task("Test subject");
        
        $this->repository->update($task);
    }
    
    /**
     * @test
     * @expectedException Kkdshka\TodoList\Repository\NotFoundException
     * @covers Kkdshka\TodoList\Repository\CsvRepository::delete
     */
    public function shouldNotDeleteNotExistedTask() {
        $task = new Task("Test subject");
        $task->setId(1);
        
        $this->repository->delete($task);
    }
    
    /**
     * @test
     * @expectedException InvalidArgumentException
     * @covers Kkdshka\TodoList\Repository\CsvRepository::delete
     */
    public function shouldNotDeleteUnsavedTask() {
        $task = new Task("Test subject");
        
        $this->repository->delete($task);
    }
    
    /**
     * @test
     * @expectedException Kkdshka\TodoList\Repository\NotFoundException
     * @expectedExceptionMessage Can't find task with id = 1
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::findTaskById
     */
    public function shouldNotFindUnsavedTaskById() {
        $this->repository->findTaskById(1);
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\CsvRepository::findTaskById
     */
    public function shouldFindTaskById() {
        $firstTask = new Task("First test subject");
        $secondTask = new Task("Second test subject");
        
        $this->repository->create($firstTask);
        $this->repository->create($secondTask);
        
        $this->assertEquals($firstTask, $this->repository->findTaskById(1));
    }
    
    /**
     * @test
     */
    public function shouldFixIdType() {
        $repository = new CsvRepository(__DIR__ . '/../data/todolist.csv');
        $this->assertCount(3, $repository->getAll());
    }
}
