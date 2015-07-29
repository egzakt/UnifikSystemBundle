<?php

namespace Unifik\SystemBundle\Listener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;

use Symfony\Component\Translation\Translator;
use Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Kernel;

use Unifik\SystemBundle\Entity\AppRepository;
use Unifik\SystemBundle\Entity\Section;
use Unifik\SystemBundle\Lib\Frontend\Core;

/**
 * Exception Listener
 */
class ExceptionListener
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var TimedTwigEngine
     */
    private $templating;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var EntityManager;
     */
    private $entityManager;

    /**
     * @var Core
     */
    private $core;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * Event handler that renders custom pages in case of a NotFoundHttpException (404)
     * or a AccessDeniedHttpException (403).
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ('dev' == $this->kernel->getEnvironment()) {
            return;
        }

        $exception = $event->getException();

        $this->request->setLocale($this->defaultLocale);
        $this->request->setDefaultLocale($this->defaultLocale);

        if ($exception instanceof NotFoundHttpException) {

            $section = $this->getExceptionSection(404, '404 Error');

            $this->core->addNavigationElement($section);

            $unifikRequest = $this->generateUnifikRequest($section);
            $this->setUnifikRequestAttributes($unifikRequest);
            $this->request->setLocale($this->request->attributes->get('_locale', $this->defaultLocale));
            $this->request->setDefaultLocale($this->request->attributes->get('_locale', $this->defaultLocale));
            $this->entityManager->getRepository('UnifikSystemBundle:Section')->setLocale($this->request->attributes->get('_locale'));

            $response = $this->templating->renderResponse('UnifikSystemBundle:Frontend/Exception:404.html.twig', array('section' => $section));
            $response->setStatusCode(404);

            $event->setResponse($response);

        } elseif ($exception instanceof AccessDeniedHttpException) {

            $section = $this->getExceptionSection(403, '403 Error');

            $this->core->addNavigationElement($section);

            $unifikRequest = $this->generateUnifikRequest($section);
            $this->setUnifikRequestAttributes($unifikRequest);

            $response = $this->templating->renderResponse('UnifikSystemBundle:Frontend/Exception:403.html.twig', array('section' => $section));
            $response->setStatusCode(403);

            $event->setResponse($response);
        }
    }

    /**
     * Generates a Unifik Request
     *
     * @param Section $section
     * @return array
     */
    private function generateUnifikRequest(Section $section)
    {
        // TODO: Fetch the right App from the database
        $unifikRequest = array(
            'sectionId' => $section->getId(),
            'appId' => 2, // Frontend
            'appCode' => '',
            'appName' => 'Frontend',
            'appSlug' => '',
        );

        return $unifikRequest;
    }

    /**
     * Set the Unifik Request attributes in the Request
     *
     * @param $unifikRequest
     */
    private function setUnifikRequestAttributes($unifikRequest)
    {
        $this->request->attributes->set('_unifikEnabled', true);
        $this->request->attributes->set('_unifikRequest', $unifikRequest);
    }

    /**
     * Return the specific Section for an Exception Event
     *
     * @param $id
     * @param $name
     *
     * @return object|Section
     */
    private function getExceptionSection($id, $name)
    {
        // TODO Use a tagging system with internal tags. ie: _section_404
        $sectionRepository = $this->entityManager->getRepository('UnifikSystemBundle:Section');
        $sectionRepository->setCurrentAppName('frontend');
        $sectionRepository->setLocale($this->defaultLocale);

        $section = $sectionRepository->find($id);

        // Doesn't exist, create the Section
        if (!$section) {
            $section = new Section();
            $section->setCurrentLocale($this->defaultLocale);
            $section->setApp($this->entityManager->getRepository('UnifikSystemBundle:App')->find(AppRepository::FRONTEND_APP_ID));
            $section->setId($id);
            $section->setName($this->translator->trans($name, array(), 'messages', $this->defaultLocale));
            $section->setActive(true);

            $this->entityManager->persist($section);

            // Force ID
            $metadata = $this->entityManager->getClassMetaData(get_class($section));
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

            $this->entityManager->flush();
        }

        return $section;
    }

    /**
     * @param Kernel $kernel
     */
    public function setKernel($kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param TimedTwigEngine $templating
     */
    public function setTemplating($templating)
    {
        $this->templating = $templating;
    }

    /**
     * @param Request|null $request
     */
    public function setRequest($request = null)
    {
        $this->request = $request;
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $defaultLocale
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param \Unifik\SystemBundle\Lib\Frontend\Core $core
     */
    public function setCore($core)
    {
        $this->core = $core;
    }
}
