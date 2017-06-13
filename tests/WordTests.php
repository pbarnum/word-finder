<?php

namespace Unlok\WordFinder\Tests;

use Unlok\WordFinder\Letter;
use Unlok\WordFinder\Word;

class WordTests extends BaseTest
{
    /**
     * Tests creating a Word object with correct arguments
     * @param $args
     * @dataProvider successWordProvider
     */
    function testSuccessCreate($args)
    {
        $testWord = $this->factory(Word::class, $args);
        $this->assertInstanceOf(Word::class, $testWord);
    }

    /**
     * Tests creating a Word object with non-integer arguments
     * @param $args
     * @dataProvider failureWordProvider
     * @throws Exception
     */
    function testFailureCreate($args)
    {
        $this->expectException('Exception');
        $this->factory(Word::class, $args);
    }

    /**
     * Tests methods associated with adding Letters to the word
     * This will test
     *   Adding a Letter
     *   Max Letter count
     *   Current Letter count
     *   Last Letter in stack
     */
    function testLetterAdditionSuite()
    {
        $word = $this->factory(Word::class, [3]);

        $this->assertEquals(3, $word->maxLetterCount());
        $this->assertEquals(0, $word->currentLetterCount());

        $letter = new Letter('a', 2, 2);
        $word->addLetter($letter);

        // Test Letter count is up by one
        $this->assertEquals(1, $word->currentLetterCount());

        // Adding the same letter twice should keep the current count the same
        $word->addLetter($letter);
        $this->assertEquals(1, $word->currentLetterCount());

        // Max remained the same
        $this->assertEquals(3, $word->maxLetterCount());

        // Assert the last letter added is the same as above
        $this->assertEquals($letter, $word->lastLetter());
    }

    /**
     * Tests if the Word is at full capacity
     */
    function testIsFull()
    {
        $word = $this->factory(Word::class, [3]);
        $word->addLetter(new Letter('a', 2, 2));
        $word->addLetter(new Letter('b', 3, 3));
        $word->addLetter(new Letter('c', 4, 4));
        $this->assertTrue($word->isFull());
    }

    /**
     * Tests if the Word resets back to its initial state
     */
    function testReset()
    {
        $word = $this->factory(Word::class, [3]);
        $word->addLetter(new Letter('a', 2, 2));
        $this->assertTrue($word->isAltered());
        $this->assertEquals(1, $word->currentLetterCount());
        $word->reset();
        $this->assertFalse($word->isAltered());
        $this->assertEquals(0, $word->currentLetterCount());
    }

    /**
     * Tests using spell check to make sure the Word is valid
     */
    function testValidWord()
    {
        $word = $this->factory(Word::class, [4]);
        $this->assertFalse($word->isValid());

        $word->addLetter(new Letter('t', 2, 2));
        $word->addLetter(new Letter('e', 3, 3));
        $this->assertFalse($word->isValid());

        $word->addLetter(new Letter('s', 4, 4));
        $word->addLetter(new Letter('t', 5, 5));
        $this->assertTrue($word->isValid());
    }

    /**
     * Tests the equality of one Word and another
     */
    function testEquals()
    {
        $w1 = $this->factory(Word::class, [2]);
        $w1->addLetter(new Letter('t', 2, 2));
        $w2 = $this->factory(Word::class, [3]);
        $w2->addLetter(new Letter('t', 2, 2));
        $w3 = $this->factory(Word::class, [4]);
        $w3->addLetter(new Letter('g', 2, 2));

        $this->assertTrue($w1->equals($w2));
        $this->assertFalse($w1->equals($w3));
    }

    /**
     * Tests to check if the Word contains a Letter by its Coordinates
     */
    function testContains()
    {
        $word = $this->factory(Word::class, [2]);
        $word->addLetter(new Letter('t', 2, 2));

        $this->assertFalse($word->contains(new Letter('t', 3, 5)));
        $this->assertTrue($word->contains(new Letter('o', 2, 2)));
    }

    /**
     * Tests to make sure the Word outputs correctly when cast to string
     */
    function testToString()
    {
        $str = 'test';
        $word = $this->factory(Word::class, [4]);
        $word->addLetter(new Letter('t', 2, 2));
        $word->addLetter(new Letter('e', 3, 3));
        $word->addLetter(new Letter('s', 4, 4));
        $word->addLetter(new Letter('t', 5, 5));

        $this->assertEquals($str, (string) $word);
    }

    /**
     * Provider for successfully building Word objects
     * @return array
     */
    function successWordProvider()
    {
        return [
            [[8]],
            [[4]],
            [[6]],
            [[3]]
        ];
    }

    /**
     * Provider for unsuccessfully building Word objects
     * @return array
     */
    function failureWordProvider()
    {
        return [
            [[null]],
            [[34.43]],
            [['test']],
            [[false]],
            [[['array']]]
        ];
    }
}
