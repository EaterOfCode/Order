<?php

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use Eater\Order\Law\Storage;

class StorageTest extends PHPUnit_Framework_TestCase {

    private $flySystem;
    private $lawFolders = ['', 'henk/is-there/'];

    public function setUp()
    {
        $this->flySystem = new Filesystem(new MemoryAdapter());
        $this->flySystem->write('law', '<?php file("test");');
        $this->flySystem->write('henk/is-there/another', '<? file("somewhere");');
    }

    private function createStorage()
    {
        return new Storage($this->flySystem, $this->lawFolders);
    }

    public function testHasNonExistentFile()
    {
        $storage = $this->createStorage();
        $this->assertFalse($storage->hasLawFile('non-existent'));
    }

    public function testHasExistentFile()
    {
        $storage = $this->createStorage();
        $this->assertTrue($storage->hasLawFile('law'));
        $this->assertTrue($storage->hasLawFile('another'));
    }

    /**
     * @depends testHasExistentFile
     */
    public function testWrapping()
    {
        $storage = $this->createStorage();
        $wrappedLaw = $storage->getLaw('law');
        $wrappedAnother = $storage->getLaw('another');

        $this->assertEquals('law', $wrappedLaw->getPath());
        $this->assertEquals('henk/is-there/another', $wrappedAnother->getPath());
        $this->assertEquals('<?php file("test");', $wrappedLaw->getContents());
        $this->assertEquals('<? file("somewhere");', $wrappedAnother->getContents());
    }
}
