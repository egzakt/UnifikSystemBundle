<?php

namespace Flexy\SystemBundle\Routing;

use JMS\I18nRoutingBundle\Router\DefaultPatternGenerationStrategy;
use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\DBAL\Connection;


class PatternGenerationStrategy extends DefaultPatternGenerationStrategy
{

    private $strategy;
    private $translator;
    private $translationDomain;
    private $locales;
    private $cacheDir;
    private $defaultLocale;

    public function __construct($strategy, TranslatorInterface $translator, array $locales, $cacheDir, $translationDomain = 'routes', $defaultLocale = 'en')
    {
        $this->strategy = $strategy;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->locales = $locales;
        $this->cacheDir = $cacheDir;
        $this->defaultLocale = $defaultLocale;
    }

    public function parentConstructor(Connection $databaseConnection)
    {
        $queryBuilder = $databaseConnection->createQueryBuilder();
        $queryBuilder->select('l.code');
        $queryBuilder->from('locale','l');
        $queryBuilder->where('l.active = 1');
        $queryBuilder->orderBy('l.ordering');


        $query = $databaseConnection->query($queryBuilder);

        while (false !== $result = $query->fetchObject()) {
            if (false == in_array($result->code,$this->locales)) {
                $this->locales[] = $result->code;
            }
        }

        parent::__construct($this->strategy,
                            $this->translator,
                            $this->locales,
                            $this->cacheDir,
                            $this->translationDomain,
                            $this->defaultLocale);
    }
}
