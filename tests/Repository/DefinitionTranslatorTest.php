<?php

namespace CommerceGuys\Addressing\Tests\Repository;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \CommerceGuys\Addressing\Repository\DefinitionTranslatorTrait
 */
class DefinitionTranslatorTest extends TestCase
{
    /**
     * @var DummyRepository
     */
    protected $repository;

    protected $definition = [
        'locale' => 'en',
        'title' => 'English title',
        'description' => 'English description',
        'translations' => [
            'fr' => [
                'title' => 'French title',
                'description' => 'French description',
            ],
        ],
    ];

    public function setUp(): void
    {
        $this->repository = new DummyRepository();
    }

    /**
     * @covers ::translateDefinition
     * @covers ::getLocaleVariants
     */
    public function testTranslation()
    {
        $definition = $this->repository->runTranslateDefinition($this->definition, 'fr');
        $expectedDefinition = [
            'locale' => 'fr',
            'title' => 'French title',
            'description' => 'French description',
        ];
        $this->assertEquals($expectedDefinition, $definition);
    }

    /**
     * @covers ::translateDefinition
     * @covers ::getDefaultLocale
     * @covers ::getLocaleVariants
     */
    public function testInvalidLocale()
    {
        $invalidLocales = [null, 'de'];
        foreach ($invalidLocales as $locale) {
            $definition = $this->repository->runTranslateDefinition($this->definition, $locale);
            $this->assertEquals($this->definition, $definition);
        }
    }

    /**
     * @covers ::translateDefinition
     * @covers ::getLocaleVariants
     */
    public function testLocaleFallback()
    {
        $definition = $this->repository->runTranslateDefinition($this->definition, 'fr_CA');
        $expectedDefinition = [
            'locale' => 'fr',
            'title' => 'French title',
            'description' => 'French description',
        ];
        $this->assertEquals($expectedDefinition, $definition);
    }
}
