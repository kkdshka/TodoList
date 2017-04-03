<?php
declare (strict_types = 1);

namespace Kkdshka\TodoList\Model;

use \PHPUnit\Framework\TestCase;

/**
 * @author Ксю
 */
class UserTest extends TestCase {
    
    /**
     * @test
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage Id had been already set.
     */
    public function shouldNotAllowChangeId() {
        $user = new User('login', 'password');
        
        $user->setId(1);
        $user->setId(2);
    }
    
}
