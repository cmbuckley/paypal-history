<?php

namespace Starsquare\PayPal\Parser;

use League\Csv\Reader;

class Csv extends AbstractParser {
    protected $fields;
    protected $conversion = array();
    protected $hold;
    protected $ignoreTypes = array('Authorisation', 'Order');

    protected function getDate(array $row) {
        $dateString = "{$row['Date']} {$row['Time']} {$row['Time zone']}";
        $date = \DateTime::createFromFormat('d/m/Y H:i:s e', $dateString);
        $date->setTimezone(new \DateTimeZone('UTC'));

        return $date;
    }

    public function loadFile($file) {
        $reader = Reader::createFromPath($file);
        $origHeader = $reader->fetchOne();
        $header = $headerCount = [];

        // append number to duplicate field names
        foreach ($origHeader as $field) {
            if  (isset($headerCount[$field])) {
                $field .= ' ' . $headerCount[$field]++;
            } else {
                $headerCount[$field] = 1;
            }

            $header[] = $field;
        }

        $defaultCurrency = $this->getOption('currency');

        foreach ($reader->getRecords($header) as $row) {
            // ignore the header record
            if ($row['Date'] == 'Date') { continue; }

            $key = count($this->data);
            $data = array(
                'date'     => $this->getDate($row),
                'name'     => $row['Name'],
                'type'     => $row['Type'],
                'currency' => $row['Currency'],
                'rate'     => 1,
                'amount'   => (int) bcmul(str_replace(',', '', $row['Amount']), '100'),
                'id'       => $row['Receipt ID'],
            );

            // are we looking at a currency conversion?
            if ($data['type'] == 'General Currency Conversion') {
                // grab some data to use later
                if (!isset($this->conversion['amount'])) {
                    $this->conversion['amount'] = $data['amount'];
                    $this->conversion['currency'] = $data['currency'];
                } else {
                    // rate depends which line was our local currency
                    if ($data['currency'] === $defaultCurrency) {
                        $this->conversion['rate'] = - $data['amount'] / $this->conversion['amount'];
                    } else {
                        $this->conversion['rate'] = - $this->conversion['amount'] / $data['amount'];
                        $this->conversion['currency'] = $data['currency'];
                    }
                }
            } else if (isset($this->hold)) {
                if ($data['type'] == 'Temporary Hold' && $data['amount'] == -$this->hold) {
                    unset($this->hold);
                }
            } else {
                if ($data['type'] == 'Temporary Hold') {
                    $this->hold = $data['amount'];

                } else if (!in_array($data['type'], $this->ignoreTypes)) {
                    $this->data[$key] = $data;
                }

                if ($data['currency'] !== $defaultCurrency) {
                    $this->conversion['key'] = $key;
                }
            }

            // do we have a valid conversion that we are ready to apply?
            if (
                isset($this->conversion['rate'], $this->conversion['key']) &&
                $this->data[$this->conversion['key']]['currency'] === $this->conversion['currency']
            ) {
                $this->data[$this->conversion['key']]['rate'] = $this->conversion['rate'];
                $this->conversion = array();
            }
        }
    }
}
