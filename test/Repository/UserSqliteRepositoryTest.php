<?php
declare (strict_types = 1);

namespace Kkdshka\TodoList\Repository;

use PHPUnit\Framework\TestCase;
use PDO;
use Phake;
use Kkdshka\TodoList\Model\User;

/**
 * @author Ксю
 */
class UserSqliteRepositoryTest extends TestCase {
    
    private $filename;
    private $repository;

    public function setUp() {
        $this->filename = tempnam(sys_get_temp_dir(), 'sqlite');
        $this->repository = new UserSqliteRepository("sqlite:" . $this->filename);
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
        $stmt = $pdo->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\UserSqliteRepository::save
     */
    public function shouldSaveUser() {
        $user = new User('login', 'password');
        
        $this->repository->create($user);
        
        $this->assertEquals(
            [[
                'id' => 1, 
                'login' => 'login', 
                'password_hash' => 'password'
            ]],
            $this->getAll()
        );
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\UserSqliteRepository::isLoginFree
     */
    public function shouldAllowUseUniqueLogin() {
        $this->assertTrue($this->repository->isLoginFree('login'));
    }
    
    /**
     * @test
     * @depends shouldSaveUser
     * @covers Kkdshka\TodoList\Repository\UserSqliteRepository::isLoginFree
     */
    public function shouldNotAllowUseRepeatedLogin() {
        $user = new User('login', 'password');
        $this->repository->create($user);
        
        $this->assertFalse($this->repository->isLoginFree('login'));
    }
    
    /**
     * @test
     * @depends shouldSaveUser
     * @covers Kkdshka\TodoList\Repository\UserSqliteRepository::find
     */
    public function shouldFindUserByLogin() {
        $expectedUser = new User('login', 'password');
        $this->repository->create($expectedUser);
        
        $actualUser = $this->repository->find('login');
        
        $this->assertEquals($expectedUser, $actualUser);
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Repository\UserSqliteRepository::find
     * @expectedException Kkdshka\TodoList\Repository\NotFoundException
     * @expectedExceptionMessage Can't find user with login - login.
     */
    public function shouldNotFindUnsavedUserByLogin() {
        $this->repository->find('login');
    }
    
}
