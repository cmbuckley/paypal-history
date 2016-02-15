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
            $phpFile = str_replace('csv', 'php', $file);
            $expected = include $phpFile;

            if (!$expected) {
                throw new \Exception("Missing file $phpFile");
            }

            $tests[basename($file)] = array($file, $expected);
        }

        return $tests;
    }
}
