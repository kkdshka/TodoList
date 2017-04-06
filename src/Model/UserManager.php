<?php
declare (strict_types = 1);

namespace Kkdshka\TodoList\Model;

use Kkdshka\TodoList\Repository\UserSqliteRepository;
use Kkdshka\TodoList\Model\User;
use Kkdshka\TodoList\Repository\NotFoundException as RepoNotFoundException;

/**
 * Manages user.
 *
 * @author kkdshka
 */
class UserManager {
    
    /**
     * User Repository.
     * 
     * @var UserSqliteRepository
     */
    private $repository;
    
    /**
     * @param UserSqliteRepository $repository 
     */
    public function __construct(UserSqliteRepository $repository) {
        $this->repository = $repository;
    }
    
    /**
     * Creates new user and saves him into repository.
     * 
     * @param string $login
     * @param string $plainPassword
     * @return User
     * @throws AlreadyExistsException When user with given login already exists.
     */
    public function register(string $login, string $plainPassword) : User {
        $password = $this->hashPassword($plainPassword);
        if (!$this->repository->isLoginFree($login)) {
            throw new AlreadyExistsException("User with login $login already exists.");
        }
        $user = new User($login, $password);
        $this->repository->create($user);
        return $user;
    }
    
    /**
     * Finds and returns user by login.
     * 
     * @param string $login
     * @return User
     */
    public function find(string $login) : User {
        try {
            return $this->repository->find($login);
        }
        catch (RepoNotFoundException $e) {
            throw new NotFoundException("Can't find user with login - '$login'.", 0, $e);
        }
    }
    
    /**
     * Checks if given password matches user's password.
     * 
     * @param User $user
     * @param string $plainPassword
     * @return bool
     */
    public function checkPassword(User $user, string $plainPassword) : bool {
        return password_verify($plainPassword, $user->getPassword());
        
    }
    
    private function hashPassword(string $plainPassword) : string {
       return password_hash($plainPassword, PASSWORD_DEFAULT);
    }
    
}
