JVal
====

A [JSON Schema](http://json-schema.org) validator written in PHP.

Features:

- Full draft 4 support (passes the whole official [test suite]
  (https://github.com/json-schema/JSON-Schema-Test-Suite), except for the
  two tests that require big nums to be treated as integers, which is not
  feasible in PHP)
- Distinct steps for references resolution, syntax parsing and data validation.

[![Build Status](https://travis-ci.org/stefk/JVal.svg?branch=master)](https://travis-ci.org/stefk/JVal)
[![Code Coverage](https://scrutinizer-ci.com/g/stefk/JVal/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/stefk/JVal/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/stefk/JVal/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/stefk/JVal/?branch=master)

Installation
------------

`composer require stefk/jval dev-master`

Basic usage
-----------

```php
$validator = JVal\Validator::buildDefault();
$violations = $validator->validate($data, $schema);
```

Data can be anything that might result from a call to `json_decode`. The schema
must be the JSON-decoded representation of a JSON Schema, i.e. a `stdClass`
instance or a boolean value.

If the schema contains relative references to external schemas (either remote
or local), the absolute URI of the base schema will probably be needed as well:

```php
$validator = JVal\Validator::buildDefault();
$violations = $validator->validate($data, $schema, 'file://path/to/the/schema');
```

CLI
---

```
bin/jval path/to/data path/to/schema
```
