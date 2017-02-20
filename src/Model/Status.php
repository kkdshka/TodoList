<?php

declare (strict_types = 1);

namespace Kkdshka\TodoList\Model;

/**
 * List of possible status for Task.
 *
 * @author Ксю
 */
class Status {

    const STATUS_NEW = "New";
    const STATUS_IN_PROGRESS = "In progress";
    const STATUS_DELAYED = "Delayed";
    const STATUS_COMPLETED = "Completed";

    private static $statuses = [
        self::STATUS_NEW,
        self::STATUS_IN_PROGRESS,
        self::STATUS_DELAYED,
        self::STATUS_COMPLETED
    ];
    
    public static function isStatus(string $status) : bool {
        return in_array($status, self::$statuses);
    }
}
