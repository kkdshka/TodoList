<?php
declare (strict_types = 1);

namespace Kkdshka\TodoList\Repository;

use PHPUnit\Framework\TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-09-14 at 18:41:17.
 */
class RepositoryFactoryTest extends TestCase {
    
    private static $filename;
    
    private $factory;
    
    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
        self::$filename = tempnam(sys_get_temp_dir(), 'fct');
    }
    
    public function setUp() {
        file_put_contents(self::$filename, "");
        $this->factory = new RepositoryFactory;
    }
    
    public static function tearDownAfterClass() {
        unlink(self::$filename);
    }
    
    /**
     * @test
     * @dataProvider shouldCreateRepositoryProvider
     */
    public function shouldCreateRepository(string $connectionUrl, string $class) {
        $repository = $this->factory->create($connectionUrl);
        $this->assertInstanceOf($class, $repository);
        $repository->close();
    }
    
    public function shouldCreateRepositoryProvider() : array {
        return [
            ["csv:" . self::$filename, CsvRepository::class],
            ["sqlite:" . self::$filename, SqliteRepository::class]
        ];
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown protocol unexisted in url unexisted:path/to/file
     * @covers Kkdshka\TodoList\Repository\RepositoryFactory::create
     */
    public function shouldNotCreateRepositoryWithUnknownProtocol() {
        $this->factory->create("unexisted:path/to/file");
    }
    
    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Empty protocol in url path/to/file/without/protocol
     * @covers Kkdshka\TodoList\Repository\RepositoryFactory::create
     */
    public function shouldNotCreateRepositoryWithoutProtocol() {
        $this->factory->create("path/to/file/without/protocol");
    }
    
}
