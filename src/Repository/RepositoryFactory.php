<?php

declare (strict_types = 1);

namespace Kkdshka\TodoList\Repository;

use RuntimeException;

/**
 * Factory which choose which repository will be used.
 *
 * @author Ксю
 */
class RepositoryFactory {
    
    /**
     * Creates new repository according to connection URL.
     * @param string $connectionUrl 
     * @return \Kkdshka\TodoList\Repository\Repository
     * @throws RuntimeException When connection URL with unknown protocol was given.
     */
    public function create(string $connectionUrl) : Repository {
        $protocol = stristr($connectionUrl, ":", true);
        switch ($protocol) {
            case "csv":
                // We use pseudo-protocol "csv" to make connection URL for CsvRepository look like PDO connection URL
                // But CsvRepository knows nothing about "csv" protocol
                $filepath = substr($connectionUrl, strlen("csv:"));
                return new CsvRepository($filepath); 
            case "sqlite":
                return new SqliteRepository($connectionUrl);
            default:
                throw new RuntimeException("Unknown protocol $protocol");
        }
        
    }
}
