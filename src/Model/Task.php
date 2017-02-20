<?php

declare (strict_types = 1);

namespace Kkdshka\TodoList\Model;

use Kkdshka\TodoList\Model\{
    Status,
    Priority
};
use BadMethodCallException;
use InvalidArgumentException;

/**
 * Holds task data.
 *
 * @author Ксю
 */
class Task {

    private $id;
    private $subject;
    private $description;
    private $priority;
    private $status;

    public function __construct(string $subject, string $description = "", int $priority = 3, string $status = "New") {
        $this->subject = $subject;
        $this->description = $description;
        $this->setPriority($priority);
        $this->setStatus($status);
    }

    public function getSubject(): string {
        return $this->subject;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getPriority(): int {
        return $this->priority;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function getId(): int {
        return $this->id;
    }
    
    public function setSubject(string $subject) {
        $this->subject = $subject;
    }

    public function setDescription(string $description) {
        $this->description = $description;
    }

    public function setPriority(int $priority) {
        if (!Priority::isPriority($priority)) {
            throw new InvalidArgumentException("Invalid priority $priority given.");
        }
        $this->priority = $priority;
    }
    
    public function setStatus(string $status) {
        if (!Status::isStatus($status)) {
            throw new InvalidArgumentException("Invalid status $status given.");
        }
        $this->status = $status;
    }

        public function setId(int $id) {
        if (isset($this->id)) {
            throw new BadMethodCallException("Id had been already set.");
        }
        $this->id = $id;
    }

    public function hasId(): bool {
        return isset($this->id);
    }
}
