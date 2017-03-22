<?php

declare (strict_types = 1);

namespace Kkdshka\TodoList\Repository;

use RuntimeException;

/**
 * Throws if entity not found in repository.
 *
 * @author kkdshka
 */
class NotFoundException extends RuntimeException {
    
}
