<?php

namespace Starsquare\Test\PayPal\Exporter;

use Starsquare\PayPal\Exporter\Csv;
use Starsquare\Test\PayPal\Parser\Mock;
use PHPUnit\Framework\TestCase;

class CsvTest extends TestCase {

    protected $defaultOptions = array(
        'timezone' => 'Europe/London',
        'dateFormat' => 'Y-m-d H:i',
        'skipBankPayments' => true,
        'currency' => 'GBP',
        'amountFormat' => '%01.2f',
        'accountName' => 'PayPal',
        'fields' => array('account', 'date', 'name', 'amount', 'currency', 'rate'),
        'separator' => ',',
    );

    /**
     * @dataProvider providerGetOutput
     */
    public function testGetOutput($data, $expectedFile) {
        $mock = new Mock;
        $mock->setData($data);
        $fixture = new Csv($mock, $this->defaultOptions);

        $this->assertStringEqualsFile($expectedFile, $fixture->getOutput());
    }

    public static function providerGetOutput() {
        $tests = array();

        foreach (glob('test/etc/php/*.php') as $file) {
            $expected = str_replace(array('php', 'csv/'), array('csv', 'csv-out/'), $file);
            $tests[basename($file)] = array(include $file, $expected);
        }

        return $tests;
    }
}

