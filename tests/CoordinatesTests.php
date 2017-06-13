<?php

namespace Unlok\WordFinder\Tests;

use Unlok\WordFinder\Coordinates;

class CoordinatesTests extends BaseTest
{
    /**
     * Tests creating a Coordinate object with integer arguments
     * @param $args
     * @dataProvider successCoordinatesProvider
     */
    function testSuccessCreate($args)
    {
        $testCoordinates = $this->factory(Coordinates::class, $args);
        $this->assertInstanceOf(Coordinates::class, $testCoordinates);
    }

    /**
     * Tests creating a Coordinate object with non-integer arguments
     * @param $args
     * @dataProvider failureCoordinatesProvider
     * @throws Exception
     */
    function testFailureCreate($args)
    {
        $this->expectException('Exception');
        $this->factory(Coordinates::class, $args);
    }

    /**
     * Test the Coordinates getter methods
     */
    function testGetters()
    {
        $obj = $this->factory(Coordinates::class, [2, 4]);
        $this->assertEquals(2, $obj->getRow());
        $this->assertEquals(4, $obj->getCol());
    }

    /**
     * Tests all outcomes of a Coordinates directions
     * @param $direction
     * @dataProvider directionsProvider
     */
    function testNeighborsSuccess($direction)
    {
        $coordinates = $this->factory(Coordinates::class, [2, 2]);
        $result = $coordinates->neighbor($direction);
        switch ($direction) {
            case 0:
                $row = 1;
                $col = 1;
                break;
            case 1:
                $row = 1;
                $col = 2;
                break;
            case 2:
                $row = 1;
                $col = 3;
                break;
            case 3:
                $row = 2;
                $col = 3;
                break;
            case 4:
                $row = 3;
                $col = 3;
                break;
            case 5:
                $row = 3;
                $col = 2;
                break;
            case 6:
                $row = 3;
                $col = 1;
                break;
            case 7:
                $row = 2;
                $col = 1;
                break;
            default:
                $this->fail('Coordinates neighbor() directional test failure.');
        }

        $this->assertEquals($row, $result->getRow());
        $this->assertEquals($col, $result->getCol());
    }

    /**
     * Tests out of bounds Exception for a Coordinates directions
     * @throws Exception
     */
    function testNeighborsFailure()
    {
        $direction = 8;
        $coordinates = $this->factory(Coordinates::class, [2, 2]);
        $this->expectException('Exception');
        $coordinates->neighbor($direction);
    }

    /**
     * Tests the Coordinates equal method to check when one object points to the same space as another
     */
    function testEquals()
    {
        $c1 = $this->factory(Coordinates::class, [2, 4]);
        $c2 = $this->factory(Coordinates::class, [2, 4]);
        $c3 = $this->factory(Coordinates::class, [3, 5]);

        $this->assertTrue($c1->equals($c2));
        $this->assertFalse($c1->equals($c3));
    }

    /**
     * Tests casting a Coordinates object to string
     */
    public function testToString()
    {
        $expected = "['row' => 2, 'col' => 4]";
        $obj = $this->factory(Coordinates::class, [2, 4]);
        $this->assertEquals($expected, (string)$obj);
    }

    /**
     * Provider for successfully building a Coordinates object
     * @return array
     */
    function successCoordinatesProvider()
    {
        return [
            [[0, 0]]
        ];
    }

    /**
     * Provider for unsuccessfully building Coordinates objects
     * @return array
     */
    function failureCoordinatesProvider()
    {
        return [
            [[null, null]],
            [['string', 'string']],
            [[123.456, 654.321]],
            [[true, false]],
            [[[1,2,3], (object)[4,5,6]]],
        ];
    }

    /**
     * Provider of all directions of a Coordinate
     * @return array
     */
    function directionsProvider()
    {
        return [
            [0],
            [1],
            [2],
            [3],
            [4],
            [5],
            [6],
            [7]
        ];
    }
}
