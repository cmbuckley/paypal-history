<?php

namespace Starsquare\PayPal\Exporter;

class Ofx extends AbstractExporter {
    protected $document;
    protected $listElement;

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
        $xpath->query('//CURDEF')->item(0)->nodeValue = $this->getOption('currency');
        $xpath->query('//ACCTID')->item(0)->nodeValue = $this->getOption('accountName');

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

    protected function processRecord(array $record) {
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
        if ($record['rate'] === 1 && $record['currency'] === $this->getOption('currency')) {
            unset($transaction['CURRENCY']);
        }

        $this->addTransaction($transaction);
    }

    protected function finishOutput() {
        return $this->getDocument()->saveXML(null, LIBXML_NOEMPTYTAG);
    }
}
