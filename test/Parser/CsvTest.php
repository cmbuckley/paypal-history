<?php

namespace Starsquare\Test\PayPal\Parser;

use Starsquare\PayPal\Parser\Csv;

class CsvTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider providerLoadFile
     */
    public function testLoadFile($csv, $expected) {
        $fixture = new Csv(array('currency' => 'GBP'));
        $fixture->loadFile($csv);

        $this->assertEquals($expected, $fixture->getData());
    }

    public function providerLoadFile() {
        $tests = array();

        foreach (glob('test/etc/csv/*.csv') as $file) {
            $expected = str_replace('csv', 'php', $file);
            $tests[basename($file)] = array($file, include $expected);
        }

        return $tests;
    }
}
