<?php

namespace Unlok\WordFinder\Tests;

use Unlok\WordFinder\Board;
use Unlok\WordFinder\Word;
use Unlok\WordFinder\WordFinder;

class WordFinderTests extends BaseTest
{
    function testSolve()
    {
        $game = $this->factory(WordFinder::class, [$this->createComplexBoard()]);
        $game->solve();
    }

    function createSimpleBoard()
    {
        $words = [new Word(4)];
        $grid = [
            ['t', 's'],
            ['e', 't']
        ];
        return new Board($grid, $words);
    }

    function createComplexBoard()
    {
        $words = [
            new Word(7),
            new Word(9),
            new Word(5),
            new Word(4),
        ];
        $grid = [
            ['e', 'l', 'f', 'a', 'n'],
            ['t', 'l', 'e', 'y', 'f'],
            ['d', 's', 'i', 'g', 'l'],
            ['r', 'a', 'l', 'b', 'e'],
            ['b', 'i', 'o', 'l', 'r'],
        ];
        return new Board($grid, $words);
    }
}
