<?php

require __DIR__ . '/vendor/autoload.php';

use Unlok\WordFinder\Board;
use Unlok\WordFinder\Word;
use Unlok\WordFinder\WordFinder;

//$words = [
//    new Word(7),
//    new Word(9),
//    new Word(5),
//    new Word(4),
//];
//$grid = [
//    ['e', 'l', 'f', 'a', 'n'],
//    ['t', 'l', 'e', 'y', 'f'],
//    ['d', 's', 'i', 'g', 'l'],
//    ['r', 'a', 'l', 'b', 'e'],
//    ['b', 'i', 'o', 'l', 'r'],
//];

//$words = [
//   new Word(4),
//   new Word(5),
//];
//$grid = [
//   ['m', 'o', 'o'],
//   ['l', 'i', 'n'],
//   ['g', 'h', 't'],
//];

// puzzle Raccoon - 1
 $words = [
     new Word(5), // pearl
     new Word(4), // ruby
     new Word(7), // diamond
 ];
 $grid = [
     ['p', 'r', 'n', 'd'],
     ['u', 'e', 'i', 'd'],
     ['a', 'l', 'y', 'o'],
     ['r', 'b', 'm', 'a'],
 ];

//$words = [new Word(4)];
//$grid = [
//    ['t', 's'],
//    ['e', 't']
//];

$board = new Board($grid, $words);
$game = new WordFinder($board);

echo "Start Game\n";
$game->solve();
