<?php

namespace Starsquare\Test\PayPal\Parser;

use Starsquare\PayPal\Parser\Csv;
use PHPUnit\Framework\TestCase;

class CsvTest extends TestCase {

    /**
     * @dataProvider providerLoadFile
     */
    public function testLoadFile($csv, $php) {
        $fixture = new Csv(array('currency' => 'GBP'));
        $fixture->loadFile($csv);

        $this->assertFileExists($php);
        $this->assertEquals(include $php, $fixture->getData());
    }

    public function providerLoadFile() {
        $tests = array();

        foreach (glob('test/etc/csv/*.csv') as $file) {
            $tests[basename($file)] = array($file, str_replace('csv', 'php', $file));
        }

        return $tests;
    }
}
