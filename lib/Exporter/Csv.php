<?php

namespace Starsquare\PayPal\Exporter;

class Csv extends AbstractExporter {

    protected $handle;
    protected $fieldNames = array();

    protected function startOutput() {
        $this->handle = fopen('php://temp', 'w');

        $fields = $this->getOption('fields');
        $this->fieldNames = array_flip($fields);

        // headings
        fputcsv($this->handle, array_map('ucwords', $fields));
    }

    protected function processRecord(array $record) {
        $record['date']    = $this->getDate($record['date']);
        $record['amount']  = $this->getAmount($record['amount']);
        $record['account'] = $this->getOption('accountName');

        // only keep the requested fields
        $record = array_intersect_key($record, $this->fieldNames);
        fputcsv($this->handle, array_replace($this->fieldNames, $record));
    }

    protected function finishOutput() {
        $output = stream_get_contents($this->handle, -1, 0);
        fclose($this->handle);
        return $output;
    }
}
