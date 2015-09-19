<?php

use Eater\Order\Law\Stream;

class StreamTest extends PHPUnit_Framework_TestCase {

    public function setUp()
    {
        Stream::register('law');
    }

    public function testOpenUnexistentStream()
    {
        $resource = fopen('law://non-existent', 'r');

        $this->assertFalse($resource);
    }

}
