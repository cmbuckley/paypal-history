<?php

namespace Starsquare\Test\PayPal;

use Starsquare\PayPal\Converter;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase {

    protected $converter;

    protected function setUp() {
        $this->converter = new Converter('test/etc/csv/basic.csv', array(
            'converter' => array(
                'parser'     => 'csv',
                'exporter'   => 'ofx',
                'dateFormat' => '\t\e\s\t',
            ),
            'exporter' => array(
                'ofx' => array(
                    'contentType' => 'text/xml',
                    'encoding' => 'utf-8',
                    'timezone' => 'UTC',
                    'dateFormat' => 'Ymd',
                    'skipBankPayments' => true,
                    'currency' => 'GBP',
                    'accountName' => 'PayPal',
                    'amountFormat' => '%01.2f',
                ),
            ),
        ));
    }

    public function testGetParser() {
        $parser = $this->converter->getParser();
        $this->assertInstanceOf('\\Starsquare\\PayPal\\Parser\\Csv', $parser);
    }

    public function testGetExporter() {
        $exporter = $this->converter->getExporter();
        $this->assertInstanceOf('\\Starsquare\\PayPal\\Exporter\\Ofx', $exporter);
    }

    public function testGetHeaders() {
        $headers = array(
            'Content-Type'        => 'text/xml; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="paypal-test.ofx"',
        );

        $this->assertSame($headers, $this->converter->getHeaders());
    }

    public function testToString() {
        $this->assertStringEqualsFile('test/etc/ofx/basic.ofx', (string) $this->converter);
    }
}
