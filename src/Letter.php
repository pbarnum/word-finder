<?php

namespace Unlok\WordFinder;

use \Exception;

/**
 * Class Letter
 *
 * @package Unlok\WordFinder
 */
class Letter
{
    /**
     * Number of directions available
     *
     * @var int
     */
    const DIRECTIONS = 8;

    /**
     * The current position of the Letter
     * @var Coordinates
     */
    protected $coordinates;

    /**
     * The string value of the Letter
     *
     * @var string
     */
    protected $value;

    /**
     * The direction of the next neighbor
     *
     * @var int
     */
    protected $direction;

    /**
     * If this Letter has been seeded
     *
     * @var bool
     */
    protected $seeded;

    /**
     * If this Letter is currently used in a Word
     *
     * @var bool
     */
    protected $currentlyUsed;

    /**
     * Letter constructor.
     *
     * @param $value
     * @param $row
     * @param $col
     * @throws Exception
     */
    public function __construct($value, $row, $col)
    {
        if (is_string($value) && preg_match('[a-zA-Z]', $value) === false) {
            throw new Exception("Letters must consist of a string value and integers for row and column.");
        }

        $this->coordinates = new Coordinates($row, $col);
        $this->value = $value;
        $this->direction = 0;
        $this->seeded = false;
        $this->currentlyUsed = false;
    }

    /**
     * Gets the Coordinates
     *
     * @return Coordinates
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Checks two Letters for equality
     *
     * @param Letter|null $l
     * @return bool
     */
    public function equals(Letter $l = null)
    {
        return $l && $this->coordinates->equals($l->getCoordinates());
    }

    /**
     * Returns the current direction
     *
     * @return int
     */
    public function currentDirection()
    {
        return $this->direction;
    }

    /**
     * Increments and returns the direction
     *
     * @return int
     */
    public function nextDirection()
    {
        return ++$this->direction;
    }

    /**
     * Resets the Letter's direction
     */
    public function resetDirection()
    {
        $this->direction = 0;
    }

    /**
     * @param Coordinates $coordinates
     */
    public function setCoordinates(Coordinates $coordinates)
    {
        $this->coordinates = $coordinates;
    }

    /**
     * @return bool
     */
    public function inUse()
    {
        return $this->currentlyUsed;
    }

    /**
     * Sets the Letter as currently in use in a Word
     */
    public function setInUse()
    {
        $this->currentlyUsed = true;
    }

    /**
     * Sets the Letter as currently not found in a Word
     */
    public function unUse()
    {
        $this->currentlyUsed = false;
    }

    /**
     * Sets the Letter as having been a Word's seed
     */
    public function touch()
    {
        $this->seeded = true;
    }

    /**
     * Checks if the Letter has been used as a seed
     *
     * @return bool
     */
    public function touched()
    {
        return $this->seeded;
    }

    /**
     * Sets the Letter as never been a seed
     */
    public function untouch()
    {
        $this->seeded = false;
    }

    /**
     * Magic method - String representation of the class
     *
     * @return string
     */
    public function __toString()
    {
        return (string) strtolower($this->value);
    }
}
