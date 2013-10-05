<?php

namespace Flexy\SystemBundle\Controller\Backend\Application;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Flexy\SystemBundle\Lib\Backend\BackendController;
use Flexy\SystemBundle\Entity\App;
use Flexy\SystemBundle\Entity\AppRepository;
use Flexy\SystemBundle\Form\Backend\ApplicationType;

/**
 * Application Controller
 */
class ApplicationController extends BackendController
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

        $this->createAndPushNavigationElement('Applications', 'flexy_system_backend_application');

        $this->appRepository = $this->getEm()->getRepository('FlexySystemBundle:App');
    }

    /**
     * Lists all root sections by navigation
     *
     * @return Response
     */
    public function listAction()
    {
        $applications = $this->appRepository->findAllExcept(AppRepository::BACKEND_APP_ID);

        return $this->render('FlexySystemBundle:Backend/Application/Application:list.html.twig', array(
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

                $this->get('flexy_system.router_invalidator')->invalidate();

                $this->addFlashSuccess($this->get('translator')->trans(
                    '%entity% has been saved.',
                    array('%entity%' => $entity))
                );

                if ($request->request->has('save')) {
                    return $this->redirect($this->generateUrl('flexy_system_backend_application'));
                }

                return $this->redirect($this->generateUrl($entity->getRoute(), $entity->getRouteParams()));
            } else {
                $this->addFlashError('Some fields are invalid.');
            }
        }

        return $this->render('FlexySystemBundle:Backend/Application/Application:edit.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * Check if we can delete an Application.
     *
     * @param Request $request
     * @param $applicationId
     *
     * @return JsonResponse
     */
    public function checkDeleteAction(Request $request, $applicationId)
    {
        $application = $this->appRepository->find($applicationId);
        $output = $this->checkDeleteEntity($application);

        return new JsonResponse($output);
    }

    /**
     * Deletes a App entity.
     *
     * @param Request $request
     * @param integer $applicationId
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $applicationId)
    {
        $application = $this->appRepository->find($applicationId);
        $this->deleteEntity($application);
        $this->get('flexy_system.router_invalidator')->invalidate();

        return $this->redirect($this->generateUrl('flexy_system_backend_application'));
    }
}
