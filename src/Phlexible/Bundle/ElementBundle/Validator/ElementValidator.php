<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Validator;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Element validator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementValidator
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ElementService     $elementService
     * @param ValidatorInterface $validator
     */
    public function __construct(ElementService $elementService, ValidatorInterface $validator)
    {
        $this->elementService = $elementService;
        $this->validator = $validator;
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @return ConstraintViolationListInterface[]
     */
    public function validate(ElementVersion $elementVersion)
    {
        $elementtype = $this->elementService->findElementtype($elementVersion->getElement());
        $elementStructure = $this->elementService->findElementStructure($elementVersion);

        $violations = array();

        $rii = new \RecursiveIteratorIterator($elementtype->getStructure()->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($rii as $node) {
            /* @var $node ElementtypeStructureNode */

            $constraints = $this->createConstraints($node);

            $violations[$node->getDsId()] = $this->validator->validate($elementStructure->getValue($node->getName()), $constraints);
        }

        return $violations;
    }

    /**
     * @param ElementtypeStructureNode $node
     *
     * @return array
     */
    private function createConstraints(ElementtypeStructureNode $node)
    {
        $constraints = array();

        $required = $node->getConfigurationValue('required');
        if ($required === 'always' || $required === 'on_publish') {
            $notNullConstraint = new NotNull();

            $constraints[] = $notNullConstraint;
        }

        $regexp = $node->getValidationValue('regexp');
        if ($regexp) {
            $regexConstraint = new Regex();
            $regexConstraint->match = $regexp;

            // ignore_case
            // multiline

            $constraints[] = $regexConstraint;
        }

        $minLength = $node->getValidationValue('min_length');
        $maxLength = $node->getValidationValue('max_length');
        if ($minLength || $maxLength) {
            $lengthConstraint = new Length();
            if ($minLength) {
                $lengthConstraint->min = $minLength;
            }
            if ($maxLength) {
                $lengthConstraint->max = $maxLength;
            }

            $constraints[] = $lengthConstraint;
        }

        $minValue = $node->getValidationValue('min_value');
        $allowNegative = $node->getValidationValue('allow_negative');
        if ($minValue || !$allowNegative) {
            $greaterThanOrEqualConstraint = new GreaterThanOrEqual();

            $value = 0;
            if ($minValue > 0) {
                $value = $minValue;
            }

            $greaterThanOrEqualConstraint->value = $value;

            $constraints[] = $greaterThanOrEqualConstraint;
        }

        if ($maxValue = $node->getValidationValue('max_value')) {
            $lessThanOrEqualConstraint = new LessThanOrEqual();
            $lessThanOrEqualConstraint->value = $maxValue;

            $constraints[] = $lessThanOrEqualConstraint;
        }

        // decimal

        return $constraints;
    }
}