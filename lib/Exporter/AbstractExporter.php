<?php

namespace Starsquare\PayPal\Exporter;

use Starsquare\PayPal\Parser\AbstractParser;

abstract class AbstractExporter {
    protected $options = array();

    public function __construct(AbstractParser $parser, array $options = array()) {
        $this->parser = $parser;
        $this->setOptions($options);
    }

    public function setOptions(array $options) {
        $this->options = array_replace_recursive($this->options, $options);
    }

    abstract public function getOutput();

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
