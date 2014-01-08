<?php

namespace Starsquare\PayPal\Parser;

use Starsquare\PayPal\Options;

abstract class AbstractParser extends Options {

    protected $data = array();

    public function __construct(array $options = array()) {
        $this->setOptions($options);
    }

    public function getData() {
        return $this->data;
    }

    abstract public function loadFile($file);

    public static function create($type, $file, array $options = array()) {
        $class = __NAMESPACE__ . '\\' . ucfirst($type);

        if (!class_exists($class)) {
            throw new \Exception("Parser type '$type' does not exist.");
        }

        $parser = new $class($options);
        $parser->loadFile($file);
        return $parser;
    }
}
