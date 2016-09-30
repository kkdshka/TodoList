<?php

declare (strict_types = 1);

namespace Kkdshka\TodoList\Repository;

use RuntimeException;

/**
 * Throws if entity not found in repository.
 *
 * @author Ксю
 */
class NotFoundException extends RuntimeException {
    
}
