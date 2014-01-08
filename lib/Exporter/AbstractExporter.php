<?php

namespace Starsquare\PayPal\Exporter;

use Starsquare\PayPal\Options;
use Starsquare\PayPal\Parser\AbstractParser;

abstract class AbstractExporter extends Options {

    // names of transactions from the connected bank account
    protected $bankPayments = array('Bank Account', 'Credit Card');

    public function __construct(AbstractParser $parser, array $options = array()) {
        $this->parser = $parser;
        $this->setOptions($options);
    }

    protected function getContentType() {
        return $this->getOption('contentType') . '; charset=' . $this->getOption('charset');
    }

    protected function shouldSkip(array $record) {
        if ($this->getOption('skipBankPayments') && in_array($record['name'], $this->bankPayments)) {
            return true;
        }

        return false;
    }

    protected function startOutput() {
    }

    abstract protected function processRecord(array $record);

    protected function finishOutput() {
        return '';
    }

    public function getOutput() {
        $this->startOutput();

        foreach ($this->parser->getData() as $record) {
            if (!$this->shouldSkip($record)) {
                $this->processRecord($record);
            }
        }

        return $this->finishOutput();
    }

    public function __toString() {
        return $this->getOutput();
    }

    public static function create($type, AbstractParser $parser, array $options = array()) {
        $class = __NAMESPACE__ . '\\' . ucfirst($type);

        if (!class_exists($class)) {
            throw new \Exception("Exporter type '$type' does not exist.");
        }

        $exporter = new $class($parser, $options);
        return $exporter;
    }
}
