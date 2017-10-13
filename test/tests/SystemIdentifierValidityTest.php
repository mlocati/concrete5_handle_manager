<?php

namespace concrete5\HandleManager\Test;

class SystemIdentifierValidityTest extends TestCase
{
    public function validSystemIdentifiersProvider()
    {
        return [
            ['sys0'],
            ['sys1'],
            ['sys2'],
            ['sys3'],
            ['sys4'],
        ];
    }

    /**
     * @dataProvider validSystemIdentifiersProvider
     */
    public function testValidSystemIdentifiers($handle)
    {
        $this->assertTrue(static::$defaultStore->isSystemIdentifierValid($handle));
    }

    public function invalidSystemIdentifiersProvider()
    {
        return [
            [null],
            [false],
            [$this],
            [''],
            [' a'],
            ['1test'],
            ['eè'],
        ];

        return $result;
    }

    /**
     * @dataProvider invalidSystemIdentifiersProvider
     */
    public function testInvalidSystemIdentifiers($handle)
    {
        $this->assertFalse(static::$defaultStore->isSystemIdentifierValid($handle));
    }
}
