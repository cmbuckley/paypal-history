<?php

namespace Starsquare\PayPal;

use Starsquare\PayPal\Parser\AbstractParser as Parser;
use Starsquare\PayPal\Exporter\AbstractExporter as Exporter;

class Converter extends Options {
    protected $file;
    protected $parser;
    protected $exporter;

    public function __construct($file, array $options) {
        $this->file = $file;
        $this->initOptions($options);
    }

    protected function initOptions(array $options) {
        $this->setOptions(parse_ini_file(__DIR__ . '/../etc/options.ini'));
        $this->setOptions($options);
    }

    public function getParser() {
        if ($this->parser === null) {
            $this->parser = Parser::create($this->getOption('parser'), $this->file);
        }

        return $this->parser;
    }

    public function getExporter() {
        if ($this->exporter === null) {
            $this->exporter = Exporter::create($this->getOption('exporter'), $this->getParser());
        }

        return $this->exporter;
    }

    public function getHeaders() {
        return array(
            'Content-Type'        => $this->getExporter()->getContentType(),
            'Content-Disposition' => sprintf(
                'attachment; filename="paypal-%s.%s"',
                date($this->getConverterOption('dateFormat')),
                $this->getConverterOption('exporter')
            ),
        );
    }

    public function sendHeaders() {
        if ($this->getConverterOption('sendHeaders')) {
            foreach ($this->getHeaders() as $field => $value) {
                header("$field: $value");
            }
        }
    }

    public function __toString() {
        try {
            $this->sendHeaders();
            return (string) $this->getExporter();
        } catch (\Exception $ex) {
            return (string) $ex;
        }
    }
}
