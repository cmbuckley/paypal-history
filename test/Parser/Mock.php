<?php

namespace Starsquare\Test\PayPal\Parser;

use Starsquare\PayPal\Parser\AbstractParser;

class Mock extends AbstractParser {
    public function setData(array $data) {
        $this->data = $data;
    }

    public function loadFile($file) {}
}
