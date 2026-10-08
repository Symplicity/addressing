<?php

namespace CommerceGuys\Addressing\Tests\Validator\Constraints;

use CommerceGuys\Addressing\Validator\Constraints\Country as CountryConstraint;
use CommerceGuys\Addressing\Validator\Constraints\CountryValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @coversDefaultClass \CommerceGuys\Addressing\Validator\Constraints\CountryValidator
 */
class CountryValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->constraint = new CountryConstraint();
        // The original tests use an empty property path. Reset it and rebuild
        // the context (and re-inject it into the validator) so that violations
        // are raised against the empty path.
        $this->propertyPath = '';
        $this->context = $this->createContext();
        $this->validator->initialize($this->context);
    }

    protected function createValidator(): \Symfony\Component\Validator\ConstraintValidatorInterface
    {
        return new CountryValidator();
    }

    /**
     * @covers \CommerceGuys\Addressing\Validator\Constraints\CountryValidator
     *
     * @uses \CommerceGuys\Addressing\Repository\CountryRepository
     */
    public function testEmptyIsValid()
    {
        $this->validator->validate(null, $this->constraint);
        $this->assertNoViolation();

        $this->validator->validate('', $this->constraint);
        $this->assertNoViolation();
    }

    /**
     * @covers \CommerceGuys\Addressing\Validator\Constraints\CountryValidator
     *
     * @uses \CommerceGuys\Addressing\Repository\CountryRepository
     */
    public function testInvalidValueType()
    {
        $this->expectException(\Symfony\Component\Validator\Exception\UnexpectedTypeException::class);
        $this->validator->validate(new \stdClass(), $this->constraint);
    }

    /**
     * @covers \CommerceGuys\Addressing\Validator\Constraints\CountryValidator
     *
     * @uses \CommerceGuys\Addressing\Repository\CountryRepository
     */
    public function testInvalidCountry()
    {
        $this->validator->validate('InvalidValue', $this->constraint);
        $this->buildViolation($this->constraint->message)
            ->setParameters(['{{ value }}' => '"InvalidValue"'])
            ->atPath('')
            ->assertRaised();
    }

    /**
     * @covers \CommerceGuys\Addressing\Validator\Constraints\CountryValidator
     *
     * @uses \CommerceGuys\Addressing\Repository\CountryRepository
     * @dataProvider getValidCountries
     */
    public function testValidCountries($country)
    {
        $this->validator->validate($country, $this->constraint);
        $this->assertNoViolation();
    }

    public function getValidCountries()
    {
        return [
            ['GB'],
            ['AT'],
            ['MY'],
        ];
    }
}
