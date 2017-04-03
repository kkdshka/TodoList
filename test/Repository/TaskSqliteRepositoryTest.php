<?php
declare (strict_types = 1);

namespace Kkdshka\TodoList\Repository;

use PHPUnit\Framework\TestCase;
use Kkdshka\TodoList\Model\{
    Task,
    User
};
use PDO;
use Phake;

/**
 * Description of CsvRepositoryTest
 *
 * @author kkdshka
 */
class TaskSqliteRepositoryTest extends TestCase {

    private $filename;
    private $repository;
    private $user;

    public function setUp() {
        $this->filename = tempnam(sys_get_temp_dir(), 'sqlite');
        $this->repository = new TaskSqliteRepository("sqlite:" . $this->filename);
        $this->user = Phake::mock(User::class);
        Phake::when($this->user)->getId()->thenReturn(1);
        Phake::when($this->user)->getLogin()->thenReturn('User');
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
        $task = new Task($this->user, "Test subject");

        $this->repository->create($task);

        $this->assertEquals(
            [[
                'id' => '1', 
                'subject' => "Test subject", 
                'description' => '', 
                'priority' => 3, 
                'status' => 'New',
                'user_id' => '1'
            ]], 
            $this->getAll()
        );
    }

    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::update
     */
    public function shouldUpdateExistedTask() {
        $task = new Task($this->user, "Test subject");
        $this->repository->create($task);
        $task->setPriority(5);
        $this->repository->update($task);
        $this->assertEquals(
            [
                [
                    'id' => '1', 
                    'subject' => "Test subject", 
                    'description' => '', 
                    'priority' => 5, 
                    'status' => 'New',
                    'user_id' => '1'
                ]
            ], 
            $this->getAll()
        );
    }

    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::delete
     */
    public function shouldDeleteExistedTask() {
        $task = new Task($this->user, "Test subject");

        $this->repository->create($task);
        $this->repository->delete($task);

        $this->assertEquals([], $this->getAll());
    }

    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::getAll
     */
    public function shouldGetByUserTasks() {
        $firstTask = new Task($this->user, "First test subject");
        $secondTask = new Task($this->user, "Second test subject");

        $this->repository->create($firstTask);
        $this->repository->create($secondTask);

        $this->assertEquals([$firstTask, $secondTask], $this->repository->getUserTasks($this->user));
    }

    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::create
     */
    public function shouldCreateTwoNewTasks() {
        $firstTask = new Task($this->user, "First test subject");
        $secondTask = new Task($this->user, "Second test subject");

        $this->repository->create($firstTask);
        $this->repository->create($secondTask);

        $this->assertEquals(
            [
                [
                    'id' => '1', 
                    'subject' => "First test subject", 
                    'description' => "", 
                    'priority' => 3, 
                    'status' => 'New',
                    'user_id' => '1'
                ],
                [
                    'id' => '2', 
                    'subject' => "Second test subject", 
                    'description' => "", 
                    'priority' => 3, 
                    'status' => 'New',
                    'user_id' => '1'
                ]
            ], 
            $this->getAll()
        );
    }

    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::update
     */
    public function shouldNotAffectOtherTasksWhenUpdate() {
        $firstTask = new Task($this->user, "First test subject");
        $secondTask = new Task($this->user, "Second test subject");
        
        $this->repository->create($firstTask);
        $this->repository->create($secondTask);
        $firstTask->setPriority(5);
        $this->repository->update($firstTask);
        
        $this->assertEquals(
            [
                [
                    'id' => '1', 
                    'subject' => "First test subject", 
                    'description' => "", 
                    'priority' => 5, 
                    'status' => 'New',
                    'user_id' => '1'
                ],
                [
                    'id' => '2', 
                    'subject' => "Second test subject", 
                    'description' => "", 
                    'priority' => 3, 
                    'status' => 'New',
                    'user_id' => '1'
                ]
            ], 
            $this->getAll()
        );
    }
    
    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::delete
     */
    public function shouldNotAffectOtherTasksWhenDelete() {
        $firstTask = new Task($this->user, "First test subject");
        $secondTask = new Task($this->user, "Second test subject");

        $this->repository->create($firstTask);
        $this->repository->create($secondTask);
        $this->repository->delete($firstTask);

        $this->assertEquals(
            [
                [
                    'id' => '2', 
                    'subject' => "Second test subject", 
                    'description' => "", 
                    'priority' => 3, 
                    'status' => 'New',
                    'user_id' => '1'
                ]
            ], 
            $this->getAll()
        );
    }

    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::getUserTasks
     */
    public function shouldReturnNewUserTasks() {
        $firstTask = new Task($this->user, "First test subject");
        $secondTask = new Task($this->user, "Second test subject");

        $this->repository->create($firstTask);
        $this->assertEquals([$firstTask], $this->repository->getUserTasks($this->user));

        $this->repository->create($secondTask);
        $this->assertEquals([$firstTask, $secondTask], $this->repository->getUserTasks($this->user));
    }

    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::getUserTasks
     */
    public function shouldNotReturnDeletedUserTasks() {
        $firstTask = new Task($this->user, "First test subject");
        $secondTask = new Task($this->user, "Second test subject");

        $this->repository->create($firstTask);
        $this->repository->create($secondTask);
        $this->assertEquals([$firstTask, $secondTask], $this->repository->getUserTasks($this->user));

        $this->repository->delete($secondTask);
        $this->assertEquals([$firstTask], $this->repository->getUserTasks($this->user));
    }

    /**
     * @test
     * @expectedException Kkdshka\TodoList\Repository\NotFoundException
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::update
     */
    public function shouldNotUpdateNotExistedTask() {
        $task = new Task($this->user, "Test subject");
        $task->setId(1);
        
        $this->repository->update($task);
    }
    
    /**
     * @test
     * @expectedException InvalidArgumentException
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::update
     */
    public function shouldNotUpdateUnsavedTask() {
        $task = new Task($this->user, "Test subject");
        
        $this->repository->update($task);
    }
    
    /**
     * @test
     * @expectedException Kkdshka\TodoList\Repository\NotFoundException
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::delete
     */
    public function shouldNotDeleteNotExistedTask() {
        $task = new Task($this->user, "Test subject");
        $task->setId(1);

        $this->repository->delete($task);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::delete
     */
    public function shouldNotDeleteUnsavedTask() {
        $task = new Task($this->user, "Test subject");

        $this->repository->delete($task);
    }

    /**
     * @test
     * @expectedException Kkdshka\TodoList\Repository\NotFoundException
     * @expectedExceptionMessage Can't find task with id = 1
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::find
     */
    public function shouldNotFindUnsavedTask() {
        $this->repository->find(1, $this->user);
    }

    /**
     * @test
     * @depends shouldCreateNewTask
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::find
     */
    public function shouldFindTask() {
        $firstTask = new Task($this->user, "First test subject");
        $secondTask = new Task($this->user, "Second test subject");

        $this->repository->create($firstTask);
        $this->repository->create($secondTask);

        $this->assertEquals($firstTask, $this->repository->find(1, $this->user));
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\SqliteRepository::getUserTasks
     * @expectedException Kkdshka\TodoList\Repository\NotFoundException
     * @expectedExceptionMessage Can't find tasks for user User.
     */
    public function shouldNotFindUnexistedUserTasks() {
        $this->repository->getUserTasks($this->user);
    }
    
}
