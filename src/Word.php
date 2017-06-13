<?php

namespace Unlok\WordFinder;

use \Exception;
use Mekras\Speller\Aspell\Aspell;
use Mekras\Speller\Source\StringSource;

class Word
{
    /**
     * The Word's Letter array
     *
     * @var array
     */
    protected $letters;

    /**
     * The Letter count
     *
     * @var int
     */
    protected $letterCount;

    /**
     * Word constructor.
     *
     * @param $letterCount
     * @param array[Letter] $hints
     * @throws Exception
     */
    public function __construct($letterCount)
    {
        if (!is_int($letterCount) || $letterCount <= 0) {
            throw new Exception("Word size must be an integer greater than 0.");
        }

        $this->letterCount = $letterCount;
        $this->letters = [];
    }

    /**
     * Resets a Word back to its initial state
     */
    public function reset()
    {
        while (!empty($letters)) {
            $this->removeLastLetter();
        }
    }

    /**
     * Returns the last letter added or null
     *
     * @return Letter|null
     */
    public function lastLetter()
    {
        return end($this->letters) ?: null;
    }

    /**
     * Adds a Letter to the end of the Word
     *
     * @param Letter $letter
     */
    public function addLetter(Letter $letter)
    {
        if (!$this->isFull() && !$this->contains($letter) && !$letter->inUse()) {
            $letter->setInUse();
            $this->letters[] = $letter;
        }
    }

    /**
     * Removes the last Letter of the Word
     */
    public function removeLastLetter()
    {
        $letter = $this->lastLetter();

        if ($letter != null) {
            $letter->unUse();
        }

        array_pop($this->letters);
    }

    /**
     * Returns the current Letter count
     *
     * @return int
     */
    public function currentLetterCount()
    {
        return count($this->letters);
    }

    /**
     * Returns the maximum number of Letters this Word can hold
     *
     * @return int
     */
    public function maxLetterCount()
    {
        return $this->letterCount;
    }

    /**
     * Checks if the Word is at capacity
     *
     * @return bool
     */
    public function isFull()
    {
        return $this->currentLetterCount() == $this->maxLetterCount();
    }

    /**
     * Checks two Words for equality
     *
     * @param Word|null $w
     * @return bool
     */
    public function equals(Word $w = null)
    {
        return $w != null && (string) $this == (string) $w;
    }

    /**
     * Checks if a Letter exists in the Word
     *
     * The compared Letters must be at the same Coordinate location
     *
     * @param Letter $l
     * @return bool
     */
    public function contains(Letter $l)
    {
        foreach ($this->letters as $letter) {
            if ($letter->equals($l)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the Word is a valid word using spell check and Letter count
     *
     * @return bool
     */
    public function isValid()
    {
        $speller = new Aspell();
        $source = new StringSource((string)$this);
        $issues = $speller->checkText($source, []);
        return !empty($source->getAsString()) && empty($issues) && $this->isFull();
    }

    /**
     * Magic method - String representation of the class
     * @return string
     */
    public function __toString()
    {
        $letters = $this->letters;
        array_walk($letters, function($letter) {
            return !empty((string) $letter) ? (string) $letter : '_';
        });
        return (string) implode('', $letters);
    }
}
