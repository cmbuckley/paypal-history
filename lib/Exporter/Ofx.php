<?php

namespace Starsquare\PayPal\Exporter;

class Ofx extends AbstractExporter {
    protected $document;
    protected $listElement;

    protected $timezone = 'Europe/London';
    protected $currency = 'GBP';
    protected $account = 'PayPal';
    protected $ignoreTransfers = true;

    public function getDocument() {
        if (!$this->document) {
            $this->document = $this->initDocument();
        }

        return $this->document;
    }

    protected function initDocument() {
        $document = new \DOMDocument;
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;
        $document->load(__DIR__ . '/../../etc/template.ofx');

        $xpath = new \DOMXPath($document);
        $xpath->query('//CURDEF')->item(0)->nodeValue = $this->currency;
        $xpath->query('//ACCTID')->item(0)->nodeValue = $this->account;

        return $document;
    }

    protected function getListElement() {
        if (!$this->listElement) {
            $xpath = new \DOMXPath($this->getDocument());
            $this->listElement = $xpath->query('//BANKTRANLIST')->item(0);
        }

        return $this->listElement;
    }

    protected function addElements(\DOMElement $parent, array $children) {
        foreach ($children as $tag => $value) {
            $element = $parent->ownerDocument->createElement($tag);

            if (is_array($value)) {
                $this->addElements($element, $value);
            } else {
                $element->nodeValue = $value;
            }

            $parent->appendChild($element);
        }
    }

    protected function addTransaction(array $record) {
        $transaction = $this->getDocument()->createElement('STMTTRN');
        $this->addElements($transaction, $record);
        $this->getListElement()->appendChild($transaction);
    }

    protected function getDate(\DateTime $date) {
        return $date->setTimezone(new \DateTimeZone($this->timezone))->format('Ymd');
    }

    protected function getAmount($amount) {
        return sprintf('%01.2f', $amount / 100);
    }

    protected function sendHeaders() {
        header('Content-Type: text/xml; charset=utf-8');
        header(vsprintf('Content-Disposition: attachment; filename="paypal-%s-%s-%s.ofx"', array_map('date', array('Y', 'm', 'd'))));
    }

    public function getOutput() {
        foreach ($this->parser->getData() as $record) {
            if ($this->ignoreTransfers && in_array($record['name'], array('Bank Account', 'Credit Card'))) {
                continue;
            }

            $transaction = array(
                'DTPOSTED' => $this->getDate($record['date']),
                'TRNAMT'   => $this->getAmount($record['amount']),
                'FITID'    => $record['id'],
                'NAME'     => $record['name'],
                'CURRENCY' => array(
                    'CURRATE' => $record['rate'],
                    'CURSYM'  => $record['currency'],
                ),
            );

            // don't need to set if default currency
            if ($transaction['CURRENCY']['CURRATE'] === 1 && $transaction['CURRENCY']['CURSYM'] === $this->currency) {
                unset($transaction['CURRENCY']);
            }

            $this->addTransaction($transaction);
        }

        $this->sendHeaders();
        return $this->getDocument()->saveXML(null, LIBXML_NOEMPTYTAG);
    }
}
