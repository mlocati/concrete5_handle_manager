<?php

namespace concrete5\HandleManager\Test;

class HandleValidityTest extends TestCase
{
    public function validHandlesProvider()
    {
        return [
            ['a'],
            ['A'],
            ['handle'],
            [str_repeat('a', 64)],
            ['ABCDEFGHIJKLMNOPQRSTUVWXYZ'],
            ['abcdefghijklmnopqrstuvwxyz'],
            ['AbCdEfGhIjKlMnOpQrStUvWxYz'],
            ['word_word_word'],
            ['word-word_word'],
        ];
    }

    /**
     * @dataProvider validHandlesProvider
     */
    public function testValidHandles($handle)
    {
        $this->assertTrue(static::$defaultStore->isHandleValid($handle));
    }

    public function invalidHandlesProvider()
    {
        $result = [
            [null],
            [false],
            [$this],
            [''],
            [' a'],
            ['1test'],
            ['eè'],
            [str_repeat('a', 65)],
        ];
        for ($ord = 0; $ord <= 255; ++$ord) {
            $char = ord($ord);
            if (strpos('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-', $char) === false) {
                $result[] = ['test_'.$char];
            }
        }

        return $result;
    }

    /**
     * @dataProvider invalidHandlesProvider
     */
    public function testInvalidHandles($handle)
    {
        $this->assertFalse(static::$defaultStore->isHandleValid($handle));
    }
}
