<?php

use Eater\Order\Law\Stream;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use Eater\Order\Law\Storage;
use Monolog\Logger;
use Monolog\Handler\NullHandler;

class StreamTest extends PHPUnit_Framework_TestCase {

    private $flySystem;
    private $lawFolders = ['', 'henk/is-there/'];

    public function setUp()
    {
        $this->flySystem = new Filesystem(new MemoryAdapter());
        $this->flySystem->write('law', '<?php file("test");');
        $this->flySystem->write('henk/is-there/another', '<? file("somewhere");');

        $logger = new Logger("Order");
        $logger->pushHandler(new NullHandler());
        Stream::setLogger($logger);
        Stream::setStorage($this->createStorage());
    }

    private function createStorage()
    {
        return new Storage($this->flySystem, $this->lawFolders);
    }

    public function testOpenUnexistentStream()
    {
        $resource = @fopen('law://non-existent', 'r');

        $this->assertFalse($resource);
    }

    public function testOpenStream()
    {
        $resource = fopen('law://law', 'r');

        $this->assertNotFalse($resource);
        $this->assertTrue(fclose($resource));
    }

    public function testFileGetContents()
    {
        $contentsLaw     = file_get_contents('law://law');
        $contentsAnother = file_get_contents('law://another');

        $this->assertEquals('<?php namespace Eater\\Order\\Law\\Wrapped; file("test");', $contentsLaw);
        $this->assertEquals('<?php namespace Eater\\Order\\Law\\Wrapped; file("somewhere");', $contentsAnother);
    }

    public function testOpenWriteMode()
    {
        $this->setExpectedException('Eater\\Order\\Law\\InvalidMode');
        fopen('law://law', 'w');
    }
}
