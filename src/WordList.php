<?php

namespace Unlok\WordFinder;

/**
 * Class WordList
 *
 * @package Unlok\WordFinder
 */
class WordList
{
    /**
     * The parent WordList
     *
     * @var WordList|null
     */
    protected $parentNode;

    /**
     * Array of WordList objects
     *
     * @var array[WordList]
     */
    protected $wordList;

    public function __construct(WordList $parentNode = null)
    {
        $this->parentNode = $parentNode;
        $this->wordList = [];
    }

    public function getWord(Word $word)
    {
        if ($this->hasChildren($word)) {
            return $this->wordList[(string) $word];
        }

        return null;
    }

    /**
     * Adds the word to the list
     *
     * @param Word $word
     */
    public function addWord(Word $word)
    {
        $value = (string) $word;
        $this->wordList[$value] = new WordList($this);
    }

    /**
     * Adds the word to the list and returns the child WordList
     *
     * @param Word $word
     * @return WordList
     */
    public function addWord(Word $word)
    {
        $this->addWord($word);
        return $this->getWord($word);
    }

    public function hasChildren(Word $word)
    {
        return !empty($this->wordList[(string) $word]);
    }

    public function getParent()
    {
        return $this->parentNode;
    }
}
