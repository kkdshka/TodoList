<?php

namespace Kkdshka\TodoList\Repository;

use Kkdshka\TodoList\Model\Task;

/**
 * Abstracts tasks persistence.
 * 
 * @author Ксю
 */
interface Repository {
    
    /**
     * Saves task.
     * 
     * @param Task $task Task to save.
     */
    function save(Task $task);
    
    /**
     * Deletes task.
     * 
     * @param Task $task Task to delete.
     */
    function delete(Task $task);
    
    /**
     * @return Task[] All tasks.
     */
    function getAll() : array;
}
