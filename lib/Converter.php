<?php

namespace Starsquare\PayPal;

use Starsquare\PayPal\Parser\AbstractParser as Parser;
use Starsquare\PayPal\Exporter\AbstractExporter as Exporter;

class Converter {
    protected $options = array();
    protected $file;
    protected $parser;
    protected $exporter;

    public function __construct($file, array $options) {
        $this->file = $file;
        $this->options = $options;
    }

    public function getParser() {
        if ($this->parser === null) {
            $this->parser = Parser::create($this->options['inputFormat'], $this->file);
        }

        return $this->parser;
    }

    public function getExporter() {
        if ($this->exporter === null) {
            $this->exporter = Exporter::create($this->options['outputFormat'], $this->getParser());
        }

        return $this->exporter;
    }

    public function __toString() {
        try {
            return (string) $this->getExporter();
        } catch (\Exception $ex) {
            return (string) $ex;
        }
    }
}
