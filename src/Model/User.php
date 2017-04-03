<?php
declare (strict_types = 1);

namespace Kkdshka\TodoList\Model;

use BadMethodCallException;

/**
 * Holds user data.
 *
 * @author kkdshka
 */
class User {
    
    private $id;
    private $login;
    private $passwordHash;


    public function __construct(string $login, string $password) {
        $this->login = $login;
        $this->passwordHash = $password;
    }
    
    public function getPassword() : string {
        return $this->passwordHash;
    }
    
    public function getLogin() : string {
        return $this->login;
    }
    
    public function setId(int $id) {
        if (isset($this->id)) {
            throw new BadMethodCallException("Id had been already set.");
        }
        $this->id = $id;
    }
    
    public function getId() : int {
        return $this->id;
    }    
    
}
