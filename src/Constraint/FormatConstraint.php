<?php

namespace JVal\Constraint;

use JVal\Constraint;
use JVal\Context;
use JVal\Exception\Constraint\InvalidTypeException;
use JVal\Types;
use JVal\Walker;
use DateTime;
use stdClass;

class FormatConstraint implements Constraint
{
    /**
     * @see http://stackoverflow.com/a/1420225
     */
    const HOSTNAME_REGEX = '/^
      (?=.{1,255}$)
      [0-9a-z]
      (([0-9a-z]|-){0,61}[0-9a-z])?
      (\.[0-9a-z](?:(?:[0-9a-z]|-){0,61}[0-9a-z])?)*
      \.?
    $/ix';

    /**
     * @see http://tools.ietf.org/html/rfc3986#appendix-B
     *
     * Original regex has been modified to reject URI references. It just
     * enforces the general structure of the URI (each part, like scheme,
     * authority, etc. should be validated separately)
     */
    const URI_REGEX = '#^(([^:/?\#]+):)?//([^/?\#]*)(\?([^\#]*))?(\#(.*))?#ix';

    public function keywords()
    {
        return ['format'];
    }

    public function supports($type)
    {
        return $type === Types::TYPE_STRING;
    }

    public function normalize(stdClass $schema, Context $context, Walker $walker)
    {
        if (!is_string($schema->format)) {
            $context->enterNode($schema->format, 'format');

            throw new InvalidTypeException($context, Types::TYPE_STRING);
        }

        // TODO: add option to treat unknown format as a schema error
    }

    public function apply($instance, stdClass $schema, Context $context, Walker $walker)
    {
        if (!is_string($instance)) {
            $context->addViolation('should be a string');
        } elseif ($schema->format === 'date-time') {
            // PHP support for RFC3339 doesn't include fractional time
            // (milliseconds) so we must add another check if needed
            if (!$this->isDateTimeValid($instance, DATE_RFC3339)
                && !$this->isDateTimeValid($instance, 'Y-m-d\TH:i:s.uP')) {
                $context->addViolation('should be a valid date-time (RFC3339)');
            }
        } elseif ($schema->format === 'email') {
            if (!filter_var($instance, FILTER_VALIDATE_EMAIL)) {
                $context->addViolation('should be a valid email');
            }
        } elseif ($schema->format === 'hostname') {
            if (!preg_match(self::HOSTNAME_REGEX, $instance)) {
                $context->addViolation('should be a valid hostname');
            }
        } elseif ($schema->format === 'ipv4') {
            if (!filter_var($instance, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $context->addViolation('should be a valid IPv4 address');
            }
        } elseif ($schema->format === 'ipv6') {
            if (!filter_var($instance, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $context->addViolation('should be a valid IPv6 address');
            }
        } elseif ($schema->format === 'uri') {
            if (!preg_match(self::URI_REGEX, $instance)) {
                $context->addViolation('should be a valid URI (RFC3986)');
            }
        }
    }

    private function isDateTimeValid($date, $format)
    {
        $dateTime = DateTime::createFromFormat($format, $date);

        if (!$dateTime) {
            return false;
        }

        $errors = DateTime::getLastErrors();

        return $errors['warning_count'] === 0 && $errors['error_count'] === 0;
    }
}
