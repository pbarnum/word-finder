<?php

namespace Unlok\WordFinder\Tests;

use \Exception;
use Unlok\WordFinder\Coordinates;
use Unlok\WordFinder\Letter;

class LetterTests extends BaseTest
{
    /**
     * Tests creating a Letter object with correct arguments
     * @param $args
     * @dataProvider successLetterProvider
     */
    function testSuccessCreate($args)
    {
        $testLetter = $this->factory(Letter::class, $args);
        $this->assertInstanceOf(Letter::class, $testLetter);
    }

    /**
     * Tests creating a Letter object with incorrect arguments
     * @param $args
     * @dataProvider failureLetterProvider
     * @throws Exception
     */
    function testFailureCreate($args)
    {
        $this->expectException('Exception');
        $this->factory(Letter::class, $args);
    }

    /**
     * Tests the get Coordinates method
     * TODO: Write board code to affect Letter position
     */
    function testGetOriginalCoordinates()
    {
        $letter = $this->factory(Letter::class, ['a', 2, 5]);
        $coordinates = new Coordinates(2, 5);

        $this->assertInstanceOf(Coordinates::class, $letter->getOriginalCoordinates());
        $this->assertTrue($coordinates->equals($letter->getOriginalCoordinates()));
    }

    /**
     * Tests the get Coordinates method
     * TODO: Write board code to affect Letter position
     */
    function testGetCurrentCoordinates()
    {
        $letter = $this->factory(Letter::class, ['a', 2, 5]);
        $coordinates = new Coordinates(2, 5);

        $this->assertInstanceOf(Coordinates::class, $letter->getCurrentCoordinates());
        $this->assertTrue($coordinates->equals($letter->getCurrentCoordinates()));
    }

    /**
     * Tests the equality against other objects
     * @param Letter|null $l
     */
    function testEquals(Letter $l = null)
    {
        $l1 = $this->factory(Letter::class, ['a', 2, 5]);
        $l2 = $this->factory(Letter::class, ['a', 2, 5]);
        $l3 = $this->factory(Letter::class, ['b', 4, 6]);
        $this->assertTrue($l1->equals($l2));
        $this->assertFalse($l1->equals($l3));
        $this->assertFalse($l1->equals());
    }

    /**
     * Tests to assert the Letter's direction doesn't change
     */
    function testCurrentDirection()
    {
        $letter = $this->factory(Letter::class, ['a', 2, 5]);
        $this->assertEquals(0, $letter->currentDirection());
    }

    /**
     * Tests to assert the Letter's direction increments by 1 while not exceeding the max
     */
    function testNextDirection()
    {
        $letter = $this->factory(Letter::class, ['a', 2, 5]);
        $this->assertEquals(1, $letter->nextDirection());
        $this->assertEquals(2, $letter->nextDirection());
        $this->assertEquals(3, $letter->nextDirection());
        $this->assertEquals(4, $letter->nextDirection());
        $this->assertEquals(5, $letter->nextDirection());
        $this->assertEquals(6, $letter->nextDirection());
        $this->assertEquals(7, $letter->nextDirection());
        $this->assertEquals(7, $letter->nextDirection());
    }

    /**
     * Tests the output when casting a Letter object to string
     * @dataProvider characterProvider
     */
    function testToString($char)
    {
        $letter = $this->factory(Letter::class, [$char, 2, 5]);
        $str = 'a';
        $this->assertEquals($str, (string) $letter);
    }

    /**
     * Provider for successfully building a Letter object
     * @return array
     */
    function successLetterProvider()
    {
        return [
            [['a', 0, 0]]
        ];
    }

    /**
     * Provider for unsuccessfully building Letter objects
     * @return array
     */
    function failureLetterProvider()
    {
        return [
            [[null, null, null]],
            [['string', 'string', 'string']],
            [[123.456, 654.321, 32.54]],
            [[true, false, true]],
            [[[1,2,3], (object)[4,5,6], [5,2,7]]],
        ];
    }

    function characterProvider()
    {
        return [
            ['a'],
            ['A']
        ];
    }
}
