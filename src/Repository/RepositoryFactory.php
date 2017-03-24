<?php
declare (strict_types = 1);

namespace Kkdshka\TodoList\Repository;

use InvalidArgumentException;

/**
 * Factory which choose which repository will be used.
 *
 * @author kkdshka
 */
class RepositoryFactory {
    
    /**
     * Creates new repository according to connection URL.
     * 
     * @param string $connectionUrl 
     * @return \Kkdshka\TodoList\Repository\Repository
     * @throws InvalidArgumentException When connection URL with unknown protocol or without protocol was given.
     */
    public function create(string $connectionUrl) : Repository {
        $protocol = stristr($connectionUrl, ":", true);
        if (empty($protocol)) {
            throw new InvalidArgumentException("Empty protocol in url $connectionUrl");
        }
        switch ($protocol) {
            case "csv":
                // We use pseudo-protocol "csv" to make connection URL for CsvRepository look like PDO connection URL
                // But CsvRepository knows nothing about "csv" protocol
                $filepath = substr($connectionUrl, strlen("csv:"));
                return new CsvRepository($filepath); 
            case "sqlite":
                return new SqliteRepository($connectionUrl);
            default:
                throw new InvalidArgumentException("Unknown protocol $protocol in url $connectionUrl");
        }
    }
    
}
