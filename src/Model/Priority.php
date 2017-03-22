<?php

declare (strict_types = 1);

namespace Kkdshka\TodoList\Model;

/**
 * List of possible priority for Task.
 * 
 * @author kkdshka
 */
class Priority {

    const HIGHEST = 5;
    const HIGH = 4;
    const NORMAL = 3;
    const LOW = 2;
    const LOWEST = 1;

    private static $priorities = [
        self::HIGHEST,
        self::HIGH,
        self::NORMAL,
        self::LOW,
        self::LOWEST
    ];

    public static function isPriority(int $priority): bool {
        return in_array($priority, self::$priorities);
    }

}
