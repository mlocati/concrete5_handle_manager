<?php

namespace concrete5\HandleManager\Test;

class ConcurrencyTest extends TestCase
{
    /**
     * @expectedException \concrete5\HandleManager\Exception\HandleAlreadyUsedInSystemsException
     */
    public function testConcurrentAssignments()
    {
        list($store1, $store2) = $this->getStores();
        $store1->setHandleUsage('handle', 'sys1');
        $store2->setHandleUsage('handle', 'sys2');
    }

    /**
     * @return HandleStore[]
     */
    private function getStores()
    {
        $store1 = static::$defaultStore;
        $store2 = static::createNewStore();
        // Check that the connection are different
        $this->assertFalse($store1->transactionActive());
        $this->assertFalse($store2->transactionActive());
        $store1->transactionBegin();
        $this->assertTrue($store1->transactionActive());
        $this->assertFalse($store2->transactionActive());
        $store1->transactionRollback();
        $this->assertFalse($store1->transactionActive());
        $this->assertFalse($store2->transactionActive());

        return [$store1, $store2];
    }
}
