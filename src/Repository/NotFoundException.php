<?php
declare (strict_types = 1);

namespace Kkdshka\TodoList\Repository;

use RuntimeException;

/**
 * In case entity not found in repository.
 *
 * @author kkdshka
 */
class NotFoundException extends RuntimeException {
    
}
