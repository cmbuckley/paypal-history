<?php

namespace Starsquare\Test\PayPal\Exporter;

use Starsquare\PayPal\Exporter\Ofx;
use Starsquare\Test\PayPal\Parser\Mock;

class OfxTest extends \PHPUnit_Framework_TestCase {

    public function testGetDocument() {
        $fixture = new Ofx(new Mock);
        $document = $fixture->getDocument();

        $this->assertInstanceOf('DOMDocument', $document);
        $this->assertSame('OFX', $document->documentElement->tagName);
    }

    /**
     * @dataProvider providerGetOutput
     */
    public function testGetOutput($data, $expectedFile) {
        $mock = new Mock;
        $mock->setData($data);
        $fixture = new Ofx($mock);

        $this->assertStringEqualsFile($expectedFile, $fixture->getOutput());
    }

    public function providerGetOutput() {
        $tests = array();

        foreach (glob('test/etc/php/*.php') as $file) {
            $expected = str_replace('php', 'ofx', $file);
            $tests[basename($file)] = array(include $file, $expected);
        }

        return $tests;
    }
}

