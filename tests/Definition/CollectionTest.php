<?php

use Eater\Order\Definition\Collection;
use Eater\Order\Definition\Dummy;
use Monolog\Logger;
use Monolog\Handler\NullHandler;

class DefinitionTest extends PHPUnit_Framework_TestCase {

    private $logger;

    public function setUp()
    {
        $logger = new Logger("Order");
        $logger->pushHandler(new NullHandler());
        $this->logger = $logger;
    }

    public function testCircularRequire()
    {
        $def = new Dummy('circular');
        $def->requires(clone $def);

        $def2 = new Dummy('double-circular');
        $def2->requires(clone $def);
        $def->requires(clone $def2);

        $collection = new Collection($this->logger);
        $collection->add($def);
        $collection->add($def2);

        $errors = $collection->validate();

        $this->assertEquals(2, count($errors));
        $this->assertContains('dummy:circular', $errors[0]->getMessage());
        $this->assertInstanceOf('Eater\Order\Definition\CircularRequire', $errors[0]);
        $this->assertContains('dummy:circular,dummy:double-circular', $errors[1]->getMessage());
        $this->assertInstanceOf('Eater\Order\Definition\CircularRequire', $errors[1]);
    }

    public function testUnresolvedRequire()
    {
        $def        = new Dummy('main');
        $unresolved = new Dummy('unresolved');
        $def->requires($unresolved);

        $collection = new Collection($this->logger);
        $collection->add($def);
        $collection->add($unresolved);

        $errors = $collection->validate();

        $this->assertEquals(1, count($errors));
        $this->assertContains('dummy:unresolved', $errors[0]->getMessage());
        $this->assertInstanceOf('Eater\Order\Definition\UnresolvedRequiredDefinition', $errors[0]);
    }

    public function testCorrectOrder()
    {
        $def1 = new Dummy('1');
        $def2 = new Dummy('2');
        $def3 = new Dummy('3');
        $def4 = new Dummy('4');
        $def2->requires(clone $def1);
        $def3->requires(clone $def1);
        $def4->requires(clone $def2);
        $def4->requires(clone $def3);

        $collection = new Collection($this->logger);
        $collection->add($def1);
        $collection->add($def2);
        $collection->add($def3);
        $collection->add($def4);

        $errors = $collection->validate();

        $this->assertEquals(0, count($errors));

        $actionChain = $collection->getActionChain();

        $this->assertEquals(4, count($actionChain));

        if (count($actionChain) === 4) {
            $this->assertEquals($def1, $actionChain[0]);
            $this->assertEquals($def2, $actionChain[1]);
            $this->assertEquals($def3, $actionChain[2]);
            $this->assertEquals($def4, $actionChain[3]);
        }
    }

    public function testUnvalidatedRequiresNotBeingAddedToActionChain()
    {
        $def1 = new Dummy('1');
        $def2 = new Dummy('2');
        $def3 = new Dummy('3');

        $def1->setErrors(["Please don't"]);
        $def2->requires(clone $def1);
        $def3->requires(clone $def2);

        $collection = new Collection($this->logger);
        $collection->add($def1);
        $collection->add($def2);
        $collection->add($def3);

        $errors = $collection->validate();
        $this->assertEquals(3, count($errors));
        $actionChain = $collection->getActionChain();
        $this->assertEquals(2, count($actionChain));
    }

    public function testIgnoredDefinition()
    {
        $def1 = new Dummy('1');

        $def1->ignore();

        $collection = new Collection($this->logger);
        $collection->add($def1);

        $errors = $collection->validate();
        $this->assertEquals(0, count($errors));
        $actionChain = $collection->getActionChain();
        $this->assertEquals(0, count($actionChain));
    }
}
