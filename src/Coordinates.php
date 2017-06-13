<?php

namespace Unlok\WordFinder;

use \Exception;

/**
 * Class Coordinates
 *
 * @package Unlok\WordFinder
 */
class Coordinates
{
    /**
     * Row
     *
     * @var int
     */
    protected $row;

    /**
     * Column
     *
     * @var int
     */
    protected $col;

    /**
     * Coordinates constructor.
     *
     * @param $row
     * @param $col
     * @throws Exception
     */
    public function __construct($row, $col)
    {
        if (!is_int($row) || !is_int($col)) {
            throw new Exception("Coordinates must consist of integer values for row and column.");
        }

        $this->row = $row;
        $this->col = $col;
    }

    /**
     * Returns the row
     *
     * @return int
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Returns the column
     *
     * @return int
     */
    public function getCol()
    {
        return $this->col;
    }

    /**
     * Returns the Coordinates of the neighbor
     *
     * @param $direction
     * @return Coordinates
     * @throws Exception
     */
    public function neighbor($direction)
    {
        $row = $this->getRow();
        $col = $this->getCol();
        switch ($direction) {
            case 0:
                --$row;
                --$col;
                break;
            case 1:
                --$row;
                break;
            case 2:
                --$row;
                ++$col;
                break;
            case 3:
                ++$col;
                break;
            case 4:
                ++$row;
                ++$col;
                break;
            case 5:
                ++$row;
                break;
            case 6:
                ++$row;
                --$col;
                break;
            case 7:
                --$col;
                break;
            default:
                throw new Exception("Position $direction does not exist!");
        }

        return new Coordinates($row, $col);
    }

    /**
     * Checks two Coordinates for equality
     *
     * @param Coordinates|null $c
     * @return bool
     */
    public function equals(Coordinates $c = null)
    {
        return $c != null && $this->row == $c->getRow() && $this->col == $c->getCol();
    }

    /**
     * Magic method - String representation of the class
     *
     * @return string
     */
    public function __toString()
    {
        return "['row' => {$this->row}, 'col' => {$this->col}]";
    }
}
