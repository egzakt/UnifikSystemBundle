<?php

namespace Flexy\SystemBundle\Controller\Backend\Translation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Catalogue\MergeOperation;

use Flexy\SystemBundle\Lib\Backend\BaseController;
use Flexy\SystemBundle\Entity\LocaleRepository;
use Flexy\SystemBundle\Entity\TokenRepository;
use Flexy\SystemBundle\Entity\Token;
use Flexy\SystemBundle\Entity\TokenTranslation;
use Flexy\SystemBundle\Entity\TokenList;
use Flexy\SystemBundle\Entity\TokenTranslationRepository;

/**
 * Translation Controller
 */
class TranslationController extends BaseController
{
    /* @var $tokenRepository TokenRepository */
    protected $tokenRepository;

    /* @var $tokenTranslationRepository TokenTranslationRepository */
    protected $tokenTranslationRepository;

    /* @var $localeRepository LocaleRepository */
    protected $localeRepository;

    /**
     * Init
     */
    public function init()
    {
        parent::init();

        // Access restricted to ROLE_BACKEND_ADMIN
        if (false === $this->get('security.context')->isGranted('ROLE_BACKEND_ADMIN')) {
            throw new AccessDeniedHttpException('You don\'t have the privileges to view this page.');
        }

        $this->tokenRepository = $this->getEm()->getRepository('FlexySystemBundle:Token');
        $this->tokenTranslationRepository = $this->getEm()->getRepository('FlexySystemBundle:TokenTranslation');
        $this->localeRepository = $this->getEm()->getRepository('FlexySystemBundle:Locale');
    }

    /**
     * Clear Language Cache
     *
     * @param null $locale
     */
    private function clearLanguageCache($locale = null)
    {
        /* @var $finder Finder */
        $finder = new Finder();
        $cacheDir = $this->container->getParameter('kernel.cache_dir');

        foreach ($finder->files()->name('/(.*)catalogue' . ($locale ? '.' . $locale : '') . '(.*)/')->in($cacheDir) as $file) {
            unlink($file);
        }
    }

    /**
     * Parse all the Twig templates and translation files and push Tokens to the Database
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function rebuildTokenAction(Request $request)
    {

        $locales = $this->localeRepository->findAll();
        $bundles = $this->container->get('kernel')->getBundles();

        foreach ($bundles as $bundle) {

            foreach ($locales as $locale) {

                // load any messages from templates
                $bundleViewPath = $bundle->getPath() . '/Resources/views/';
                $templateCatalogue = new MessageCatalogue($locale->getCode());
                $extractor = $this->container->get('translation.extractor');
                file_exists($bundleViewPath) ? $extractor->extract($bundleViewPath, $templateCatalogue) : null;

                // load any existing messages from the translation files
                $bundleTransPath = $bundle->getPath() . '/Resources/translations';
                $configCatalogue = new MessageCatalogue($locale->getCode());
                $loader = $this->container->get('translation.loader');
                file_exists($bundleTransPath) ? $loader->loadMessages($bundleTransPath, $configCatalogue) : null;

                // show compiled list of messages
                foreach ($templateCatalogue->getDomains() as $domain) {
                    $allKeys = array_keys($templateCatalogue->all($domain));
                    foreach ($allKeys as $tokenName) {
                        $tokenName = html_entity_decode($tokenName);

                        // Only add simple translations
                        if (preg_match('/[^A-Za-z0-9- ]+/', $tokenName)) {
                            continue;
                        }

                        $token = $this->tokenRepository->findOneBy(array('token' => $tokenName));

                        if (!$token) {
                            $token = new Token();
                            $token->setToken($tokenName);

                            $this->getEm()->persist($token);
                            $this->getEm()->flush();
                        }

                        foreach ($configCatalogue->all($domain) as $configToken => $configTranslationName) {

                            if ($tokenName == $configToken) {

                                $translation = $this->tokenTranslationRepository->findOneBy(array('token' => $token, 'locale' => $locale->getCode()));

                                if (!$translation) {
                                    $translation = new TokenTranslation();
                                    $translation->setActive(true);
                                    $translation->setLocale($locale->getCode());
                                    $translation->setDomain('messages');
                                    $translation->setToken($token);
                                    $translation->setName($configTranslationName);

                                    $token->addTranslation($translation);

                                    $this->getEm()->persist($translation);
                                }
                            }
                        }

                        $this->getEm()->flush();
                        $this->getEm()->clear();
                    }
                }
            }
        }

        $this->addFlashSuccess($this->get('translator')->trans('Tokens have been updated.'));

        return $this->redirect($this->generateUrl('flexy_system_backend_translation'));
    }

    /**
     * Show translations based on tokens
     *
     * NB: We do not use Forms because they slow down the page and cause problems with more tokens
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function listAction(Request $request)
    {
        $tokens = $this->tokenRepository->findAll();
        $locales = $this->localeRepository->findAll();

        $tokenList = new TokenList();

        foreach ($tokens as $token) {

            foreach ($locales as $locale) {
                $translation = $token->translationExist($locale->getCode());

                if (!$translation) {
                    $translation = new TokenTranslation();

                    $translation->setActive(true);
                    $translation->setLocale($locale->getCode());
                    $translation->setDomain('messages');
                    $translation->setToken($token);

                    $token->addTranslation($translation);
                }
            }

            // Sort translations in case the locale's order changed in the ArrayCollection
            $token->sortTranslations($locales);

            $tokenList->addToken($token);
        }

        return $this->render('FlexySystemBundle:Backend/Translation/Translation:list.html.twig', array(
            'locales' => $locales,
            'tokenList' => $tokenList,
        ));
    }

    /**
     * Insert TokenTranslations off an AJAX Request
     *
     * @param Request $request
     * @return Response
     */
    public function insertAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $formTokenList = $request->request->get('token_list');

            foreach ($formTokenList as $tokens) {

                foreach ($tokens as $tokenId => $translations) {

                    $token = $this->tokenRepository->findOneBy(array('id' => $tokenId));

                    if (!$token) {
                        $response = array(
                            'success' => false,
                            'message' => $this->get('translator')->trans('Token does not exist.')
                        );

                        return new JsonResponse($response);
                    }

                    foreach ($translations as $locale => $name) {

                        if ($name != '') {
                            $translation = $token->translationExist($locale);

                            if (!$translation) {
                                $translation = new TokenTranslation();

                                $translation->setActive(true);
                                $translation->setLocale($locale);
                                $translation->setDomain('messages');
                                $translation->setToken($token);

                                $token->addTranslation($translation);
                            }

                            $translation->setName($name);

                            $this->getEm()->persist($translation);
                            $this->getEm()->flush();


                            $this->clearLanguageCache();
                        }
                    }
                }
            }

        }

        $response = array(
            'success' => true,
            'message' => $this->get('translator')->trans('Tokens have been updated.')
        );

        return new JsonResponse($response);
    }

}
