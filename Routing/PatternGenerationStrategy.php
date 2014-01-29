<?php

namespace Unifik\SystemBundle\Routing;

use Doctrine\DBAL\Connection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Translation\TranslatorInterface;

use JMS\I18nRoutingBundle\Router\PatternGenerationStrategyInterface;

/**
 * This pattern generation strategy is similar to the JMS DefaultPatternGenerationStrategy.
 * The locales handling has been modified to automatically merge the locales from the locale database table.
 */
class PatternGenerationStrategy implements PatternGenerationStrategyInterface
{
    const STRATEGY_PREFIX = 'prefix';
    const STRATEGY_PREFIX_EXCEPT_DEFAULT = 'prefix_except_default';
    const STRATEGY_CUSTOM = 'custom';

    protected $strategy;
    protected $translator;
    protected $translationDomain;
    protected $locales;
    protected $cacheDir;
    protected $defaultLocale;

    public function __construct($strategy, TranslatorInterface $translator, array $locales, $cacheDir, $translationDomain = 'routes', $defaultLocale = 'en', $databaseConnection = null)
    {
        $this->strategy = $strategy;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->locales = $locales;
        $this->cacheDir = $cacheDir;
        $this->defaultLocale = $defaultLocale;

        $this->mergeLocalesFromDatabase($databaseConnection);
    }

    /**
     * Fetch the active locales from the database and merge them
     *
     * @param Connection $databaseConnection
     */
    private function mergeLocalesFromDatabase($databaseConnection)
    {
        $query = $databaseConnection->createQueryBuilder()
            ->select('l.code')
            ->from('locale', 'l')
            ->where('l.active = 1')
            ->orderBy('l.ordering');

        $locales = $databaseConnection->fetchAll($query->getSQL());

        foreach ($locales as $locale) {
            if (false == in_array($locale['code'], $this->locales)) {
                $this->locales[] = $locale['code'];
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function generateI18nPatterns($routeName, Route $route)
    {
        $patterns = array();
        foreach ($route->getOption('i18n_locales') ?: $this->locales as $locale) {
            // if no translation exists, we use the current pattern
            if ($routeName === $i18nPattern = $this->translator->trans($routeName, array(), $this->translationDomain, $locale)) {
                $i18nPattern = $route->getPattern();
            }

            // prefix with locale if requested
            if (self::STRATEGY_PREFIX === $this->strategy
                || (self::STRATEGY_PREFIX_EXCEPT_DEFAULT === $this->strategy && $this->defaultLocale !== $locale)) {
                $i18nPattern = '/'.$locale.$i18nPattern;
            }

            $patterns[$i18nPattern][] = $locale;
        }

        return $patterns;
    }

    /**
     * {@inheritDoc}
     */
    public function addResources(RouteCollection $i18nCollection)
    {
        foreach ($this->locales as $locale) {
            if (file_exists($metadata = $this->cacheDir.'/translations/catalogue.'.$locale.'.php.meta')) {
                foreach (unserialize(file_get_contents($metadata)) as $resource) {
                    $i18nCollection->addResource($resource);
                }
            }
        }
    }
}
