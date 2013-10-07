<?php

namespace Flexy\SystemBundle\Translation;

use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Finder\Finder;

use Flexy\SystemBundle\Entity\TokenTranslation;
use Flexy\SystemBundle\Entity\TokenTranslationRepository;

class TranslationEditorLoader implements LoaderInterface
{
    /* @var $translationRepository TokenTranslationRepository */
    protected $translationRepository;

    protected $cacheDir;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, $appDir)
    {
        $this->translationRepository = $entityManager->getRepository("FlexySystemBundle:TokenTranslation");

        $this->cacheDir = $appDir . '/cache';
    }

    public function load($resource, $locale, $domain = 'messages')
    {
        // Clear translation cache
        $this->clearLanguageCache($locale);

        $translations = $this->translationRepository->findBy(array('locale' => $locale));

        /* @var $catalogue MessageCatalogue */
        $catalogue = new MessageCatalogue($locale);

        /* @var $translation TokenTranslation */
        foreach($translations as $translation){
            $catalogue->set($translation->getToken()->getToken(), $translation->getName(), $domain);
        }

        return $catalogue;
    }

    /**
     * Clear Language Cache
     */
    private function clearLanguageCache($locale)
    {
        /* @var $finder Finder */
        $finder = new Finder();

        foreach ($finder->files()->name('/(.*)catalogue.' . $locale . '(.*)/')->in($this->cacheDir) as $file) {
            unlink($file);
        }
    }
}