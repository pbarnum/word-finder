<?php

// the game board
$board = [
    ['t','e','f','c','a','e'],
    ['i','t','r','o','r','c'],
    ['c','r','e','m','r','n'],
    ['k','e','c','m','s','t'],
    ['c','o','u','i','a','d'],
    ['t','n','p','d','w','o']
];
$direction = ['nw', 'n', 'ne', 'e', 'se', 's', 'sw', 'w'];
$word = [];
$results = [];
$seed = new Point(3, 4);
$processed = 0;


$numberOfLetters = 7;
$word[] = $seed;

while (!empty($word) && $seed->equals($word[0])) {
    fillLetters();

    $str = arrToStr();
    if (wordIsValid($str)) {
        $results[] = $str;
    }

    array_pop($word);
}

echo "Processed: $processed\nResults count: " . count($results) . "\n";
var_dump($results);
exit(0);



function arrToStr()
{
    global $word;
    return implode('', $word);
}

function fillLetters()
{
    global $word, $seed, $numberOfLetters, $processed;
    $c = 0;

    if (empty($word)) {
        $word[] = $seed;
    }

    while (count($word) < $numberOfLetters && count($word) != 0)
    {
        $tail = end($word);
        $tail = $tail->createNextPoint();
        if ($tail) {
            $word[] = $tail;
        } else {
            array_pop($word);
            $processed++;
        }
    }
}

function directionExists($row, $col)
{
    global $board;
    return isset($board[$row][$col]);
}

function letterAlreadyInWord($row, $col)
{
    global $word;
    $testPoint = new Point($row, $col);
    return !empty(array_filter($word, function($point) use($testPoint) {
        return $point->equals($testPoint);
    }));
}

function wordIsValid(string $word)
{
    $pspell_link = pspell_new("en");
    return !empty($word) && pspell_check($pspell_link, $word);

    return false;
    $appId = '2aab5ad3';
    $appKey = 'fa38867497ae5119827a56d961662627';
    $baseUrl = 'https://od-api.oxforddictionaries.com/api/v1';
    $lang = 'en';
    $uri = "/entries/$lang/" . strtolower($word);

    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'app_id: ' . $appId,
        'app_key: ' . $appKey
    ]);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode == 200;
}



class Point
{
    protected $id;
    protected $value;
    protected $row;
    protected $col;
    protected $currentDirection;

    public function __construct($row, $col)
    {
        global $board;

        $this->row = $row;
        $this->col = $col;
        $this->value = $board[$row][$col];
        $this->id = (string) $row + (string) $col;
        $this->currentDirection = -1;
    }

    public function getId()
    {
        return (string) $this->id;
    }

    public function getRow()
    {
        return $this->row;
    }

    public function getCol()
    {
        return $this->col;
    }

    public function equals(Point $point = null)
    {
        return $point != null && $this->row == $point->getRow() && $this->col == $point->getCol();
    }

    protected function hasNextDirection()
    {
        global $direction;
        while (++$this->currentDirection < count($direction)) {
            if (self::letterExistsFromDirection($this->row, $this->col, $this->currentDirection)) {
                return true;
            }
        }

        return false;
    }

    public function createNextPoint()
    {
        if ($this->hasNextDirection()) {
            extract(self::getCoordsFromDirection($this->row, $this->col, $this->currentDirection));
            return new Point($row, $col);
        }
        return null;
    }

    protected static function getCoordsFromDirection(int $row, int $col, int $pos)
    {
        switch ($pos) {
            case 0: return ['row' => $row - 1, 'col' => $col - 1];
            case 1: return ['row' => $row - 1, 'col' => $col];
            case 2: return ['row' => $row - 1, 'col' => $col + 1];
            case 3: return ['row' => $row, 'col' => $col + 1];
            case 4: return ['row' => $row + 1, 'col' => $col + 1];
            case 5: return ['row' => $row + 1, 'col' => $col];
            case 6: return ['row' => $row + 1, 'col' => $col - 1];
            case 7: return ['row' => $row, 'col' => $col - 1];
            default: return ['row' => 0, 'col' => 0];
        }
    }

    protected static function letterExistsFromDirection(int $r, int $c, int $pos)
    {
        extract(self::getCoordsFromDirection($r, $c, $pos));
        return directionExists($row, $col) && !letterAlreadyInWord($row, $col);
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}
