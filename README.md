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

Installation
------------

`composer require stefk/jval dev-master`

Basic usage
-----------

```php
$validator = JVal\Validator::buildDefault();
$violations = $validator->validate($data, $schema);
```

Data can be anything resulting from a call to `json_decode`. The schema
must be the JSON-decoded representation of a JSON Schema, i.e. a `stdClass`
instance.

If the schema contains relative references to external schemas (either remote
or local), the absolute URI of the base schema might be needed as well:

```php
$validator = JVal\Validator::buildDefault();
$violations = $validator->validate($data, $schema, 'file://path/to/the/schema');
```
