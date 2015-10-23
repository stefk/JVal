<?php

namespace JsonSchema\Constraint;

use JsonSchema\Constraint;
use JsonSchema\Context;
use JsonSchema\Exception\Constraint\InvalidTypeException;
use JsonSchema\Types;
use JsonSchema\Walker;
use DateTime;
use stdClass;

class FormatConstraint implements Constraint
{
    /**
     * @see http://stackoverflow.com/a/1420225
     */
    const HOSTNAME_REGEX = '/^(?=.{1,255}$)[0-9a-z](?:(?:[0-9a-z]|-){0,61}[0-9a-z])?(?:\.[0-9a-z](?:(?:[0-9a-z]|-){0,61}[0-9a-z])?)*\.?$/i';

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
            $dateTime = DateTime::createFromFormat(DateTime::RFC3339, $instance);

            if (!$dateTime || $dateTime->format(DateTime::RFC3339) !== $instance) {
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
            // TODO: implement an RFC3986-compliant validation (this one is RFC2396)

            if (!filter_var($instance, FILTER_VALIDATE_URL)) {
                $context->addViolation('should be a valid URI');
            }
        }
    }
}
