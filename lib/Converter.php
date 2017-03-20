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

    protected function parseIniFile($file) {
        $options = array();

        foreach (parse_ini_file($file) as $name => $value) {
            $context = &$options;

            foreach (explode('.', $name) as $key) {
                if (!isset($context[$key])) {
                    $context[$key] = array();
                }

                $context = &$context[$key];
            }

            $context = $value;
        }

        return $options;
    }

    protected function initOptions(array $options) {
        $this->setOptions($this->parseIniFile(__DIR__ . '/../etc/options.ini'));
        $this->setOptions($options);
    }

    public function getConverterOption($name, $default = null) {
        $converterOptions = $this->getOption('converter');
        return (isset($converterOptions[$name]) ? $converterOptions[$name] : $default);
    }

    public function getHelperOptions($helper) {
        $type = $this->getConverterOption($helper);
        $helperOptions = $this->getOption($helper, array());
        $options = array();

        foreach (array('common', $type) as $key) {
            if (isset($helperOptions[$key])) {
                $options = array_merge($options, $helperOptions[$key]);
            }
        }

        return $options;
    }

    public function getParser() {
        if ($this->parser === null) {
            $this->parser = Parser::create(
                $this->getConverterOption('parser'),
                $this->file,
                $this->getHelperOptions('parser')
            );
        }

        return $this->parser;
    }

    public function getExporter() {
        if ($this->exporter === null) {
            $this->exporter = Exporter::create(
                $this->getConverterOption('exporter'),
                $this->getParser(),
                $this->getHelperOptions('exporter')
            );
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
            $output = (string) $this->getExporter();
            $this->sendHeaders();
            return $output;
        } catch (\Exception $ex) {
            return (string) $ex;
        }
    }
}
