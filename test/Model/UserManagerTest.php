<?php
declare (strict_types = 1);

namespace Kkdshka\TodoList\Model;

use PHPUnit\Framework\TestCase;
use Phake;
use Kkdshka\TodoList\Repository\UserSqliteRepository;

/**
 * @author Ксю
 */
class UserManagerTest extends TestCase {
    
    private $repository;
    private $userManager;
    
    public function setUp() {
        $this->repository = Phake::mock(UserSqliteRepository::class);
        $this->userManager = new UserManager($this->repository);
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Model\UserManager::register
     */
    public function shouldRegister() {
        Phake::when($this->repository)->isLoginFree('user')->thenReturn(true);
        
        $user = $this->userManager->register('user', 'password');
        
        Phake::verify($this->repository)->create($user);
        $this->assertEquals('user', $user->getLogin());
        $this->assertNotEquals('password', $user->getPassword());
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Model\UserManager::verifyPassword
     */
    public function shouldVerifyPassword() {
        Phake::when($this->repository)->isLoginFree('user')->thenReturn(true);
        
        $user = $this->userManager->register('user', 'password');
        
        $this->assertTrue($this->userManager->checkPassword($user, 'password'));
        $this->assertFalse($this->userManager->checkPassword($user, 'wrong_password'));
    }
    
    /**
     * @test
     * @covers Kkdshka\TodoList\Model\UserManager::register
     * @expectedException Kkdshka\TodoList\Model\AlreadyExistsException
     * @expectedExceptionMessage User with login user already exists.
     */
    public function shouldNotRegisterWhenLoginAlreadyExists() {
        Phake::when($this->repository)->isLoginFree()->thenReturn(False);
        
        $this->userManager->register('user', 'password');
    }
    
}
