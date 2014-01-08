<?php

namespace Starsquare\PayPal;

abstract class Options {
    protected $options = array();

    public function setOptions(array $options) {
        $this->options = array_replace_recursive($this->options, $options);
    }

    public function getOption($option, $default = null) {
        return (isset($this->options[$option]) ? $this->options[$option] : $default);
    }
}
