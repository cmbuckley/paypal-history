---
layout: showcase
title: PayPal History
travis: true
---
The PayPal History Converter parses any of PayPal’s downloadable history formats
and outputs the data in many other standard formats for use in your finance
management application.

## Compatibility

As well as being limited to a 2-year export, the PayPal history downloads are
incomplete at best. The table below shows the features available in each format:

|                | CSV | TXT | QIF | IIF | PDF |
|----------------|:---:|:---:|:---:|:---:|:---:|
| Currency       |  ✓  |  ✓  |     |     |  ✓  |
| Payee          |  ✓  |  ✓  |     |  ✓  |  ✓  |
| Timezone       |  ✓  |  ✓  |     |     |     |
| Transaction ID |     |     |     |     |  ✓  |
| Memo           |     |     |     |  ✓  |     |
| eBay Fee       |     |     |  ✓  |  ✓  |  ✓  |

Other things to note:

* CSV and TXT have a column for ID, but it is always empty.
* QIF only exports transactions that were made in USD. Other transactions are missing.
* IIF exports transactions for all currencies, but does not list the currency in the
    output. Amounts are in the currency of the transaction, making the format useless.
* PDF is limited by file size, but this is unlikely to be an issue for personal users.

## Foreign Currencies

A typical transaction in anything other than your default currency may look something
like this:

| Details            | Currency | Amount |
|--------------------|----------|-------:|
| European Widgets   | EUR      | -10.00 |
| From British Pound | EUR      |  10.00 |
| To Euro            | GBP      |  -8.85 |
| Credit Card        | GBP      |   8.85 |

In this example, the purchase in Euros is redeemed by a credit card payment in Sterling,
but there are 4 entries to illustrate this. By contrast, the converter calculates the
exchange rate used for the transaction and includes it in the output. The converted
output would therefore look like this:

| Details            | Currency | Amount | Rate  |
|--------------------|----------|-------:|------:|
| European Widgets   | EUR      | -10.00 | 0.885 |
| Credit Card        | GBP      |   8.85 | 1.000 |

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
