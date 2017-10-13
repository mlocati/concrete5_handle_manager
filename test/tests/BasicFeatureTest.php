<?php

namespace concrete5\HandleManager\Test;

class BasicFeatureTest extends TestCase
{
    public function testAssign()
    {
        $store = static::$defaultStore;
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys0'));
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys1'));
        $store->setHandleUsage('handle', 'sys0', []);
        $this->assertTrue($store->isHandleUsedInSystem('handle', 'sys0'));
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys1'));
        $store->setHandleUsage('handle', 'sys0', []);
        $this->assertTrue($store->isHandleUsedInSystem('handle', 'sys0'));
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys1'));
    }

    public function testUnassign()
    {
        $store = static::$defaultStore;
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys0'));
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys1'));
        $store->unsetHandleUsage('handle', 'sys0');
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys0'));
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys1'));
    }

    public function testAssigned()
    {
        $store = static::$defaultStore;
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys0'));
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys1'));
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys2'));
        $store->setHandleUsage('handle', 'sys0', []);
        $store->setHandleUsage('handle', 'sys0', []);
        $this->assertTrue($store->isHandleUsedInSystem('handle', 'sys0'));
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys1'));
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys2'));
        $store->setHandleUsage('handle', 'sys1', []);
        $store->setHandleUsage('handle', 'sys1', []);
        $this->assertTrue($store->isHandleUsedInSystem('handle', 'sys0'));
        $this->assertTrue($store->isHandleUsedInSystem('handle', 'sys1'));
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys2'));
        $store->unsetHandleUsage('handle', 'sys0', []);
        $store->unsetHandleUsage('handle', 'sys0', []);
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys0'));
        $this->assertTrue($store->isHandleUsedInSystem('handle', 'sys1'));
        $this->assertFalse($store->isHandleUsedInSystem('handle', 'sys2'));
    }

    /**
     * @expectedException \concrete5\HandleManager\Exception\HandleAlreadyUsedInSystemsException
     */
    public function testAssignFailure()
    {
        $store = static::$defaultStore;
        $store->setHandleUsage('handle', 'sys0');
        $store->setHandleUsage('handle', 'sys1');
    }
}
