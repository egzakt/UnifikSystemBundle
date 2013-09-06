<?php

namespace Egzakt\SystemBundle\Controller\Backend\Application;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Egzakt\SystemBundle\Entity\App;
use Egzakt\SystemBundle\Entity\AppRepository;
use Egzakt\SystemBundle\Form\Backend\ApplicationType;
use Egzakt\SystemBundle\Lib\Backend\BaseController;

/**
 * Application Controller
 */
class ApplicationController extends BaseController
{
    /**
     * @var AppRepository
     */
    protected $appRepository;

    /**
     * Init
     */
    public function init()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_DEVELOPER')) {
            throw new AccessDeniedHttpException();
        }

        parent::init();

        $this->createAndPushNavigationElement('Applications', 'egzakt_system_backend_application');

        $this->appRepository = $this->getEm()->getRepository('EgzaktSystemBundle:App');
    }

    /**
     * Lists all root sections by navigation
     *
     * @return Response
     */
    public function listAction()
    {
        $applications = $this->appRepository->findAllExcept(AppRepository::BACKEND_APP_ID);

        return $this->render('EgzaktSystemBundle:Backend/Application/Application:list.html.twig', array(
            'applications' => $applications
        ));
    }

    /**
     *
     *
     * @param integer $applicationId
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function editAction($applicationId, Request $request)
    {
        $entity = $this->appRepository->find($applicationId);

        if (false == $entity) {
            $entity = new App();
            $entity->setContainer($this->container);
        }

        $this->pushNavigationElement($entity);

        $form = $this->createForm(new ApplicationType(), $entity);

        if ('POST' == $request->getMethod()) {

            $form->submit($request);

            if ($form->isValid()) {

                $this->getEm()->persist($entity);
                $this->getEm()->flush();

                $this->get('egzakt_system.router_invalidator')->invalidate();

                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans(
                    '%entity% has been updated.',
                    array('%entity%' => $entity))
                );

                if ($request->request->has('save')) {
                    return $this->redirect($this->generateUrl('egzakt_system_backend_application'));
                }

                return $this->redirect($this->generateUrl($entity->getRoute(), $entity->getRouteParams()));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Some fields are invalid.');
            }
        }

        return $this->render('EgzaktSystemBundle:Backend/Application/Application:edit.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * Deletes a App entity.
     *
     * @param Request $request
     * @param integer $applicationId
     *
     * @throws NotFoundHttpException
     *
     * @return Response|RedirectResponse
     */
    public function deleteAction(Request $request, $applicationId)
    {
        $application = $this->appRepository->find($applicationId);

        if (!$application) {
            throw $this->createNotFoundException('Unable to find the App entity.');
        }

        if ($request->get('message')) {
            $template = $this->renderView('EgzaktSystemBundle:Backend/Core:delete_message.html.twig', array(
                'entity' => $application
            ));

            return new Response(json_encode(array(
                'template' => $template,
                'isDeletable' => $application->isDeletable()
            )));
        }

        $this->addFlash('success', $this->get('translator')->trans('%entity% has been deleted.', array(
            '%entity%' => $application
        )));

        $this->getEm()->remove($application);
        $this->getEm()->flush();

        $this->get('egzakt_system.router_invalidator')->invalidate();

        return $this->redirect($this->generateUrl('egzakt_system_backend_application'));
    }
}
