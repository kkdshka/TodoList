<?php

namespace Kkdshka\TodoList\Model;

use \PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-08-15 at 19:13:06.
 */
class TaskTest extends TestCase {

    /**
     * @test
     * @covers Kkdshka\TodoList\Model\Task::complete
     */
    public function shouldCompleteTask() {
        $task = new Task('Test subject');
        $this->assertFalse($task->isCompleted());
        
        $task->complete();
        
        $this->assertTrue($task->isCompleted());
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Model\Task::setId
     */
    public function shouldSetId() {
        $task = new Task('Test subject');
        
        $task->setId(1);
        
        $this->assertEquals(1, $task->getId());
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Model\Task::setId
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Id had been already set.
     */
    public function shouldNotAllowChangeId() {
        $task = new Task('Test subject');
        
        $task->setId(1);
        $task->setId(2);
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Model\Task::hasId
     */
    public function shouldDetermineIfTaskHasId() {
        $task = new Task('Test subject');
        
        $this->assertFalse($task->hasId());
        
        $task->setId(1);
        
        $this->assertTrue($task->hasId());
    }
}
