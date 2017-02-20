<?php
namespace Kkdshka\TodoList\Model;

use PHPUnit\Framework\TestCase;
use Phake;
use Kkdshka\TodoList\Repository\Repository;
use Kkdshka\TodoList\Model\Task;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-08-15 at 19:38:39.
 */
class TaskManagerTest extends TestCase
{
    /**
     * @test
     * @covers Kkdshka\TodoList\Model\TaskManager::create
     */
    public function shouldCreateTask() {
        $repository = Phake::mock(Repository::class);
        $task = new Task('Test subject');
        $taskManager = new TaskManager($repository);
        
        $taskManager->create($task);
        
        Phake::verify($repository)->create(new Task('Test subject'));
    }

    /**
     * @test
     * @covers Kkdshka\TodoList\Model\TaskManager::update
     */
    public function shouldUpdateTask() {
        $repository = Phake::mock(Repository::class);
        $task = new Task('Test subject');
        $taskManager = new TaskManager($repository);
        
        $taskManager->update($task);
        
        Phake::verify($repository)->update(new Task('Test subject'));
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Model\TaskManager::delete
     */
    public function shouldDeleteTask() {
        $repository = Phake::mock(Repository::class);
        $task = Phake::mock(Task::class);
        $taskManager = new TaskManager($repository);
        
        $taskManager->delete($task);
        
        Phake::verify($repository)->delete($task);
    }

    /**
     * @test
     * @covers Kkdshka\TodoList\Model\TaskManager::getAll
     */
    public function shouldGetAllTasks() {
        $repository = Phake::mock(Repository::class);
        $taskManager = new TaskManager($repository);
        $expectedTasks = [
            Phake::mock(Task::class),
            Phake::mock(Task::class)
        ];
        
        Phake::when($repository)->getAll()->thenReturn($expectedTasks);
        
        $tasks = $taskManager->getAll();
        
        Phake::verify($repository)->getAll();
        $this->assertEquals($expectedTasks, $tasks);
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Model\TaskManager::shouldFindTaskById
     */
    public function shouldFindTaskById() {
        $repository = Phake::mock(Repository::class);
        $task = Phake::mock(Task::class);
        $taskManager = new TaskManager($repository);
        
        Phake::when($repository)->findTaskById(1)->thenReturn($task);
        
        $this->assertEquals($task, $taskManager->findTaskById(1));
    }
}
