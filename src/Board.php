<?php

namespace Unlok\WordFinder;

use \Closure;
use \Exception;

/**
 * Class Board
 *
 * @package Unlok\WordFinder
 */
class Board
{
    /**
     * The game Board
     *
     * @var array[array[Letter]]
     */
    protected $grid;

    /**
     * The original Board
     *
     * @var
     */
    protected $originalGrid;

    /**
     * The history of each Board iteration
     *
     * @var array
     */
    protected $gridHistory;

    /**
     * All Words found within the Board
     *
     * @var array[Word]
     */
    protected $words;

    /**
     * The current Word
     *
     * @var int
     */
    protected $currentWordIndex;

    /**
     * The current seed in use
     *
     * @var
     */
    protected $seedCoordinates;

    /**
     * Board constructor.
     *
     * @param array $grid
     * @param array $words
     * @throws Exception
     */
    public function __construct(array $grid, array $words)
    {
        $letterCount = 0;
        $dimensionCount = count($grid);

        // Make sure the provided grid is square
        foreach ($grid as $row => $letters) {
            if (count($letters) != $dimensionCount) {
                throw new Exception("Grid must be a square.");
            }

            $this->grid[$row] = [];
            foreach ($letters as $col => $letter) {
                ++$letterCount;
                $this->grid[$row][$col] = new Letter($letter, $row, $col);
            }
        }

        // Make sure all Words provided are valid
        foreach ($words as $word) {
            if (!$word instanceof Word) {
                throw new Exception("All word elements must be an instance of Word.");
            }

            // Subtract letter count from number of letters in the grid
            $letterCount -= $word->maxLetterCount();
        }

        // The count of all Words must equal the number of letters in the Board
        if ($letterCount != 0) {
            throw new Exception(
                "Inconsistency between number of letters on the board and the sum of the word's letter counts."
            );
        }

        $this->gridHistory = [];
        $this->originalGrid = $this->grid;

        $this->words = $words;
        $this->currentWordIndex = 0;
        $this->setSeed();
    }

    public function getWordLengths()
    {
        return (array) array_map(function($word) {
            return $word->maxLetterCount();
        }, $this->words);
    }

    /**
     * Returns the number of words in the Board
     *
     * @return int
     */
    public function wordCount()
    {
        return count($this->words);
    }

    /**
     * Returns the current Word in focus
     *
     * @return int
     */
    public function getCurrentWordIndex()
    {
        return $this->currentWordIndex;
    }

    /**
     * Saves the current Board in history
     */
    protected function writeHistory()
    {
        $this->gridHistory[] = $this->grid;
        $history = [];
        $this->loopBoard(function ($letter, $row, $col) use (&$history) {
            $clone = clone $letter;
            $clone->untouch();
            $history[$row][$col] = $clone;
        });
        $this->setSeed();
        $this->grid = $history;
    }

    /**
     * Sets the current Board to the last saved grid
     */
    public function reverseHistory()
    {
        $this->resetCurrentWord();
        $this->previousWord();
        $this->grid = count($this->gridHistory) > 1 ? array_pop($this->gridHistory) : $this->originalGrid;
    }

    /**
     * Checks if the Board is completed
     *
     * The Board is complete when all Letters have been seeded
     *
     * @return bool
     */
    public function boardCompleted()
    {
        $allSeeded = true;
        $this->loopBoard(function ($letter) use (&$allSeeded) {
            if (!$letter->touched()) {
                $allSeeded = false;
                return;
            }
        });

//        foreach ($this->originalGrid as $row => $letters) {
//            if (!$allSeeded) {
//                break;
//            }
//
//            foreach ($letters as $col => $letter) {
//                if (!$letter->touched()) {
//                    $allSeeded = false;
//                    break;
//                }
//            }
//        }

        return $allSeeded;
    }

    /**
     * Returns the current Word in focus
     *
     * @return Word
     */
    public function currentWord()
    {
        return $this->words[$this->currentWordIndex];
    }

    /**
     * Sets the current Word
     *
     * This method wraps the list using modulus
     *
     * @param $direction
     */
    protected function setWordIndex($direction)
    {
        $newIndex = $this->currentWordIndex + $direction;
        $this->currentWordIndex = ($newIndex >= 0 ? $newIndex : 0) % count($this->words);
    }

    /**
     * Moves to the next Word in the list
     *
     * This method wraps the list using modulus
     *
     * @return Word
     */
    public function nextWord()
    {
        $this->setWordIndex(1);
        return $this->currentWord();
    }

    /**
     * Moves to the previous Word in the list
     *
     * This method wraps the list using modulus
     *
     * @return Word
     */
    public function previousWord()
    {
        $this->setWordIndex(-1);
        return $this->currentWord();
    }

    /**
     * Resets the current Word back to its initial state
     */
    public function resetCurrentWord()
    {
        $this->currentWord()->reset();
    }

    /**
     * Resets the Board's Letter directions
     */
    protected function resetLetterDirections()
    {
        $this->loopBoard(function ($letter) {
            $letter->resetDirection();
        });
    }

    public function allLettersUsed()
    {
        $used = $this->loopBoard(function ($letter) {
            if (!$letter->inUse()) {
                return false;
            }
        });

        return $used === false ? false : true;
    }

    /**
     * Loops through the Board from top down, left right
     *
     * @param Closure $func
     * @param Coordinates|null $start
     * @return null
     */
    protected function loopBoard(Closure $func, Coordinates $start = null)
    {
        foreach ($this->grid as $row => $letters) {
            foreach ($letters as $col => $letter) {
                // Start from the beginning or where specified
                if ($start == null || ($row >= $start->getRow() && $col >= $start->getCol())) {
                    $return = $func($letter, $row, $col);
                    if ($return !== null) {
                        return $return;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Loops through the Board from bottom up, left right
     *
     * @param Closure $func
     * @param Coordinates|null $start
     * @return mixed|null
     */
    protected function loopBoardReverse(Closure $func, Coordinates $start = null)
    {
        for ($row = count($this->grid) - 1; $row >= 0; --$row) {
            foreach ($this->grid[$row] as $col => $letter) {
                // Start from the beginning or where specified
                if ($start == null || ($row <= $start->getRow() && $col <= $start->getCol())) {
                    $return = $func($letter, $row, $col);
                    if ($return != null) {
                        return $return;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Returns the Coordinates of the current seed
     *
     * @return Coordinates|null
     */
    public function getCurrentSeed()
    {
        return $this->seedCoordinates;
    }

    /**
     * Finds and sets the next seed Coordinates
     *
     * @return Coordinates
     */
    protected function nextSeed()
    {
        $this->resetLetterDirections();

        $this->setNextSeed();

        $row = $this->seedCoordinates->getRow();
        $col = $this->seedCoordinates->getCol();

        return new Coordinates($row, $col);
    }

    /**
     * Sets the next seed Coordinates
     *
     * @param Coordinates|null $coordinates
     */
    protected function setSeed(Coordinates $coordinates = null)
    {
        if (!$coordinates) {
            $coordinates = new Coordinates(0, 0);
        }

        $this->seedCoordinates = $coordinates;
    }

    /**
     * Sets the next seed Coordinates or jumps back to the previous Word and Board history
     */
    protected function setNextSeed()
    {
        $pickMe = $this->loopBoard(function ($letter) {
            if (!$letter->touched() && !$letter->inUse()) {
                return $letter->getCoordinates();
            }
        });

        if ($pickMe != null) {
            $this->setSeed($pickMe);
        } else {
            $this->setSeed();
            if (!$this->boardCompleted()) {
                $this->reverseHistory();
                $this->currentWord()->removeLastLetter();
            }
        }
    }

    /**
     * Adds an adjacent Letter (relative to the last Letter) to the Word
     *
     * @return bool
     */
    public function addNeighboringLetterToWord()
    {
        if ($this->currentWord() == '') {
            $nextSeed = $this->getLetterByCoordinates($this->nextSeed());
            $nextSeed->touch();
            $this->currentWord()->addLetter($nextSeed);
        }

        $letterCount = $this->currentWord()->currentLetterCount();
        $letter = $this->getNeighboringLetter($this->currentWord()->lastLetter());
        if ($letter != null) {
            $this->currentWord()->addLetter($letter);
        } else {
            $this->currentWord()->removeLastLetter();
        }

        return $this->currentWord()->currentLetterCount() > $letterCount;
    }

    /**
     * Checks if a Letter's neighbor exists
     *
     * @param Letter $letter
     * @return bool
     */
    public function checkIfNeighborExists(Letter $letter)
    {
        $coordinates = $letter->getCoordinates();
        return $this->getLetterByCoordinates($coordinates->neighbor($letter->currentDirection())) ? true : false;
    }

    /**
     * Returns a Letter located by its Coordinates
     *
     * @param Coordinates $coordinates
     * @return Letter|null
     */
    protected function getLetterByCoordinates(Coordinates $coordinates)
    {
        return $this->loopBoard(function ($letter) use ($coordinates) {
            if ($letter->getCoordinates() == $coordinates) {
                return $letter;
            }
        });
    }

    /**
     * Returns the next neighboring Letter
     *
     * @param Letter|null $letter
     * @return Letter|null
     */
    protected function getNeighboringLetter(Letter $letter = null)
    {
        while ($letter != null && $letter->currentDirection() < Letter::DIRECTIONS) {
            $neighbor = $this->getLetterByCoordinates($letter->getCoordinates()->neighbor($letter->currentDirection()));
            $letter->nextDirection();
            if ($neighbor != null && !$neighbor->inUse()) {
                if ($this->currentWord()->contains($neighbor)) {
                    continue;
                }

                $neighbor->resetDirection();
                return $neighbor;
            }
        }

        return null;
    }

    /**
     * Drops all Board Letters down when Letters are used in a Word
     */
    public function dropLetters()
    {
        $this->writeHistory();

        $this->loopBoardReverse(function ($letter, $row, $col) {
            if ($letter->inUse()) {
                $rowAbove = $row - 1;
                do {
                    $letterAbove = $this->getLetterByCoordinates(new Coordinates($rowAbove, $col));
                    if ($letterAbove != null && !$letterAbove->inUse()) {
                        $this->swapLetters($letter, $letterAbove);
                    }
                } while (--$rowAbove >= 0);
            }
        });
    }

    /**
     * Swaps a Letter with its neighbor above
     *
     * @param Letter $first
     * @param Letter|null $second
     */
    protected function swapLetters(Letter $first, Letter $second = null)
    {
        if ($second != null) {
            $secondCoords = $second->getCoordinates();
            $second->setCoordinates($first->getCoordinates());
            $first->setCoordinates($secondCoords);
            $this->grid[$first->getCoordinates()->getRow()][$first->getCoordinates()->getCol()] = $first;
            $this->grid[$second->getCoordinates()->getRow()][$second->getCoordinates()->getCol()] = $second;
        }
    }

    /**
     * Prints the Board with an indicator of what Letters are currently used
     *
     * @return string
     */
    public function __toString()
    {
        $str = "\n";
        $grd = array_fill(
            0,
            count($this->grid),
            array_fill(0, count($this->grid), '#')
        );

        $this->loopBoard(function($letter) use (&$grd) {
            if (!$letter->inUse()) {
                $c = $letter->getCoordinates();
                $grd[$c->getRow()][$c->getCol()] = (string)$letter;
            }
        });

        foreach ($grd as $r) {
            foreach ($r as $c) {
                $str .= "$c ";
            }
            $str .= "\n";
        }

        return $str;
    }
}
