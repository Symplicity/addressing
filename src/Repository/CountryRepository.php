<?php

namespace CommerceGuys\Addressing\Repository;

/**
 * Provides the country list, sourced from commerceguys/intl or symfony/intl.
 *
 * Choosing the source at runtime allows integrations (such as the symfony
 * bundle) to stay agnostic about the intl library they need.
 */
class CountryRepository implements CountryRepositoryInterface
{
    /**
     * The country repository, if commerceguys/intl is used.
     *
     * @var \CommerceGuys\Intl\Country\CountryRepository
     */
    protected $countryRepository;

    /**
     * Whether symfony/intl is used as the source of country data.
     *
     * @var bool
     */
    protected $useSymfonyIntl = false;

    /**
     * Creates a CountryRepository instance.
     */
    public function __construct()
    {
        if (class_exists('\CommerceGuys\Intl\Country\CountryRepository')) {
            $this->countryRepository = new \CommerceGuys\Intl\Country\CountryRepository();
        } elseif (class_exists('\Symfony\Component\Intl\Countries')) {
            $this->useSymfonyIntl = true;
        } else {
            throw new \RuntimeException('No source of country data found: symfony/intl or commerceguys/intl must be installed.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList($locale = null)
    {
        if ($this->countryRepository) {
            $countryNames = $this->countryRepository->getList($locale);
        } else {
            $locale = $this->canonicalizeLocale($locale);
            $countryNames = \Symfony\Component\Intl\Countries::getNames($locale);
        }

        return $countryNames;
    }

    /**
     * Canonicalize the given locale.
     *
     * Note: commerceguys/intl does this internally, so this method only
     * needs to be invoked when using symfony/intl.
     *
     * @param string $locale The locale.
     *
     * @return string The canonicalized locale.
     */
    protected function canonicalizeLocale($locale = null)
    {
        if (is_null($locale)) {
            return $locale;
        }

        $locale = str_replace('-', '_', strtolower($locale));
        $localeParts = explode('_', $locale);
        foreach ($localeParts as $index => $part) {
            if ($index === 0) {
                // The language code should stay lowercase.
                continue;
            }

            if (strlen($part) == 4) {
                // Script code.
                $localeParts[$index] = ucfirst($part);
            } else {
                // Country or variant code.
                $localeParts[$index] = strtoupper($part);
            }
        }

        return implode('_', $localeParts);
    }
}
