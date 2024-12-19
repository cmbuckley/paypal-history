<?php

namespace Starsquare\Test\PayPal\Exporter;

use Starsquare\PayPal\Exporter\Ofx;
use Starsquare\Test\PayPal\Parser\Mock;
use PHPUnit\Framework\TestCase;

class OfxTest extends TestCase {

    protected $defaultOptions = array(
        'timezone' => 'UTC',
        'dateFormat' => 'Ymd',
        'skipBankPayments' => true,
        'currency' => 'GBP',
        'accountName' => 'PayPal',
        'amountFormat' => '%01.2f',
    );

    public function testGetDocument() {
        $fixture = new Ofx(new Mock, $this->defaultOptions);
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
        $fixture = new Ofx($mock, $this->defaultOptions);

        $this->assertStringEqualsFile($expectedFile, $fixture->getOutput());
    }

    public static function providerGetOutput() {
        $tests = array();

        foreach (glob('test/etc/php/*.php') as $file) {
            $expected = str_replace('php', 'ofx', $file);
            $tests[basename($file)] = array(include $file, $expected);
        }

        return $tests;
    }
}

