<?php

namespace Unlok\WordFinder;

/**
 * Class WordFinder
 *
 * @package Unlok\WordFinder
 */
class WordFinder
{
    /**
     * The current version
     *
     * @var string
     */
    const VERSION = 'v1.0.0';

    /**
     * The game Board
     *
     * @var Board
     */
    protected $board;

    /**
     * Array of valid Words
     *
     * @var array
     */
    protected $wordsProcessed;

    /**
     * A tracking variable for the current Word index
     *
     * @var int
     */
    protected $trackingIndex;

    /**
     * The app timer
     *
     * @var int
     */
    protected $time;

    /**
     * Log file location
     *
     * @var string
     */
    protected static $logFile;

    /**
     * WordFinder constructor.
     *
     * @param Board $board
     * @param string $logfile
     */
    public function __construct(Board $board, $logfile = '')
    {
        $this->board = $board;
        $this->wordsProcessed = [];
        $this->trackingIndex = 0;
        $this->time = 0;

        self::$logFile = !empty($logfile) ? $logfile : __DIR__ . '/../logs/word_finder_' . date('Y-m-d') . '.log';
        self::touchLog();
    }

    /**
     * Starts the app timer
     */
    protected function startTime()
    {
        $this->time = time();
    }

    /**
     * Stops the app timer and calculates run time in seconds
     */
    protected function stopTime()
    {
        $this->time = $this->getCurrentTime();
    }

    /**
     * Calculates run time in seconds
     */
    protected function getCurrentTime()
    {
        $seconds = time() - $this->time;
        return gmdate('H:i:s', $seconds);
    }

    /**
     * Finds all the valid Words in the Board
     */
    public function solve()
    {
        $this->startTime();

        $row = 0;
        while (!$this->board->boardCompleted()) {
            $this->fillLetters();

            if ($this->board->currentWord()->isValid()) {
                // Increment $trackingIndex when a new Word in the same or lower index has been found
                if ($this->board->getCurrentWordIndex() <= $this->trackingIndex) {
                    ++$row;
                }

                $this->trackingIndex = $this->board->getCurrentWordIndex();

                $currentWordIndex = $this->board->getCurrentWordIndex();
                $word = (string) $this->board->currentWord();

                $this->writeToLog("[{$this->getCurrentTime()}] Found: $word");

                $this->wordsProcessed[$row][$currentWordIndex] = $word;

                // Go back a word and drop a Letter to continue
                if ($this->board->allLettersUsed()) {
                    $this->board->reverseHistory();
                } else {
                    $this->board->dropLetters();
                    $this->board->nextWord();
                }
            }

            $this->board->currentWord()->removeLastLetter();
        }

        $this->stopTime();
        $this->printStats();
    }

    /**
     * Fills the current Word with Letters
     */
    protected function fillLetters()
    {
        while (!$this->board->currentWord()->isFull()) {
            $letter = $this->board->currentWord()->lastLetter();
            if ($letter == null || $letter->currentDirection() < Letter::DIRECTIONS) {
                $this->board->addNeighboringLetterToWord();
            } else {
                $this->board->currentWord()->removeLastLetter();
            }

            if ($this->board->boardCompleted()) {
                break;
            }
        }
    }

    /**
     * Prints the app stats
     */
    protected function printStats()
    {
        $wordCount = $this->countWordsProcessed();
        $str = "\nIt took {$this->time} to process the game board.\nFound {$wordCount} words.\n";
        $str .= $this->formatWordsFound();
        self::writeToLog($str);
    }

    /**
     * Formats the output
     *
     * @return string
     */
    protected function formatWordsFound()
    {
        // Get word metadata
        $output = '';
        $padding = 2;

        // Build the border string
        $border = '';
        foreach ($this->board->getWordLengths() as $length) {
            $border .= '+' . str_pad('', $length + $padding, '-');
        }
        $border .= '+';

        $rowCount = count($this->wordsProcessed);

        end($this->wordsProcessed);
        $lastKey = key($this->wordsProcessed);
        foreach ($this->wordsProcessed as $row => $words) {
            // Create empty array elements for the blank words
            $words = $this->arrayPadPreserveKeys($words);

            $rowCountLength = strlen((string) $rowCount);
            $prepend = '+' . str_pad('', ($rowCountLength + $padding), '-');
            $output .= "{$prepend}{$border}\n";
            $output .= '| ' . str_repeat(' ', $rowCountLength - strlen((string)$row)) . $row . ' ';

            while (!empty($words)) {
                $word = array_shift($words);
                $index = count($this->board->getWordLengths()) - count($words) - 1;

                if (!empty($word)) {
                    $output .= "| {$word} ";
                } else {
                    $output .= '|' . str_pad('', ($this->board->getWordLengths()[$index] + $padding), ' ');
                }
            }

            $output .= "|\n";

            if ($lastKey == $row) {
                $output .= "{$prepend}{$border}\n";
            }
        }

        return $output;
    }

    protected function arrayPadPreserveKeys(array $words)
    {
        $arr = [];
        for ($i = 0; $i < $this->board->wordCount(); ++$i) {
            $arr[$i] = isset($words[$i]) ? $words[$i] : '';
        }
        return $arr;
    }

    /**
     * Returns the number of Words found
     *
     * @return int
     */
    protected function countWordsProcessed()
    {
        $count = 0;
        foreach ($this->wordsProcessed as $words) {
            $count += count($words);
        }
        return $count;
    }

    /**
     * Prints a header to the log file
     */
    protected static function touchLog()
    {
        $log = "\n\n==============================\n";
        $width = 30;

        $version = 'Version ' . self::VERSION;
        $version .= str_repeat(' ', $width - strlen($version) - 4);

        $log .= "* Word Finder                *\n";
        $log .= "* Written by Patrick Barnum  *\n";
        $log .= "* $version *\n";
        $log .= "==============================";

        $dir = dirname(self::$logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 644, true);
        }

        self::writeToLog($log);
    }

    /**
     * Writes to the log file
     *
     * @param $log
     */
    protected static function writeToLog($log)
    {
        file_put_contents(self::$logFile, $log . "\n", FILE_APPEND);
    }
}
