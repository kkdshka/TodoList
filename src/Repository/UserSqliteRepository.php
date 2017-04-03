<?php
declare (strict_types = 1);

namespace Kkdshka\TodoList\Repository;

use Kkdshka\TodoList\Model\User;
use PDO;

/**
 * Sqlite Repository for users.
 *
 * @author kkdshka
 */
class UserSqliteRepository {
    
    /**
     * PDO object.
     * 
     * @var PDO
     */
    private $pdo;
    
    /**
     * @param string $connectionUrl Path to storage.
     */
    public function __construct(string $connectionUrl) {
        $this->pdo = new PDO($connectionUrl);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $query = "CREATE TABLE IF NOT EXISTS users("
            . "id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "
            . "login VARCHAR NOT NULL, "
            . "password_hash VARCHAR NOT NULL)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
    }
    
    /**
     * Saves user in db.
     * 
     * @param User $user 
     */
    public function create(User $user) {
        $this->inTransaction(function() use ($user) {
            $stmt = $this->pdo->prepare("INSERT INTO users (login, password_hash) VALUES (:login, :password_hash)");
            $params = ['login' => $user->getLogin(), 'password_hash' => $user->getPassword()];
            $stmt->execute($params);
            $user->setId((int) $this->pdo->lastInsertId());
        });
    }
    
    /**
     * Checks if login is unique.
     * 
     * @param string $login
     * @return bool
     */
    public function isLoginFree(string $login) : bool {
        $stmt = $this->pdo->query("SELECT id FROM users WHERE login = :login");
        $stmt->execute(['login' => $login]);
        $count = $stmt->rowCount();
        return ($count === 0) ? true : false;
    }
    
    /**
     * Finds user in db by login.
     * 
     * @param string $login
     * @return User
     * @throws NotFoundException When can't find user with given login.
     */
    public function find(string $login) : User {
        $stmt = $this->pdo->query("SELECT * FROM users WHERE login = :login");
        $stmt->execute(['login' => $login]);
        $userData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($userData)) {
            throw new NotFoundException("Can't find user with login - $login.");
        } 
        $user = new User($userData[0]['login'], $userData[0]['password_hash']);
        $user->setId((int) $userData[0]['id']);
        return $user;
    }
    
    /**
     * Executes several actions in one transaction.
     * @param callable $block Actions to execute.
     * @throws Exception If error occured while executing block.
     */
    private function inTransaction(callable $block) {
        $this->pdo->beginTransaction();

        try {
            $block();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        $this->pdo->commit();
    }
    
    /**
     * Close PDO.
     */
    public function close() {
        $this->pdo = null;
    }
    
}
