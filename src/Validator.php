<?php

namespace JsonSchema;

use JsonSchema\Constraint\ItemConstraint;
use JsonSchema\Constraint\MaximumConstraint;
use JsonSchema\Constraint\MaxItemsConstraint;
use JsonSchema\Constraint\MultipleOfConstraint;
use stdClass;

class Validator
{
    /**
     * @var ConstraintInterface[]
     */
    private $constraints;

    public function __construct()
    {  
       $this->constraints = [
           new MultipleOfConstraint(),
           new MaximumConstraint(),
           new MaxItemsConstraint(),
           new ItemConstraint()
       ];
    }

    public function validate($instance, stdClass $schema)
    {
        $context = new Context();
        $this->doValidate($instance, $schema, $context);

        return $context->getViolations();
    }

    // validator doesn't enforce schema correctness during normal
    // data validation : its behaviour is undefined if schema
    // is invalid
    public function validateSchema(stdClass $schema)
    {
        // get spec schema
        // doValidate($schema, $specSchema, [...])
    }

    private function doValidate($instance, stdClass $schema, Context $context)
    {
        if (isset($schema->definitions)) {
            // store sub schemas
        }

        foreach ($this->constraints as $constraint) {
            if ($constraint->isApplicableTo($instance)) {
                foreach ($constraint->keywords() as $keyword) {
                    if (isset($schema->{$keyword})) {
                        $constraint->apply($instance, $schema, $context);
                        break;
                    }
                }
            }
        }
    }
}
