# PayPal History [![Build Status](https://travis-ci.org/starsquare/paypal-history.png)](https://travis-ci.org/starsquare/paypal-history)

The PayPal History Converter parses any of PayPal’s downloadable history formats
and outputs the data in many other standard formats for use in your finance
management application.

## Compatibility

As well as being limited to a 2-year export, the PayPal history downloads are
incomplete at best. The table below shows the features available in each format:

               | CSV | TXT | QIF | IIF | PDF
---------------|:---:|:---:|:---:|:---:|:---:
Currency       |  ✓  |  ✓  |     |     |  ✓
Payee          |  ✓  |  ✓  |     |  ✓  |  ✓
Timezone       |  ✓  |  ✓  |     |     |
Transaction ID |     |     |     |     |  ✓
Memo           |     |     |     |  ✓  |
eBay Fee       |     |     |  ✓  |  ✓  |  ✓

Other things to note:

* CSV and TXT have a column for ID, but it is always empty.
* QIF only exports transactions that were made in USD. Other transactions are missing.
* IIF exports transactions for all currencies, but does not list the currency in the
    output. Amounts are in the currency of the transaction, making the format useless.
* PDF is limited by file size, but this is unlikely to be an issue for personal users.

## Output Formats

The following output formats are supported:

* OFX (v1 and v2)
* QIF
* CSV

### OFX

OFX provides support for multiple currencies and accurate times.

### QIF

QIF files do not support multiple currencies or accurate times, but the format is
used by a number of finance management applications.

### CSV

The output of the CSV format can be customised to support multiple finance management
applications. The field order, date/time format and field separator can all be altered.
