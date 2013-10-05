<?php

namespace Flexy\SystemBundle\Controller\Backend\Locale;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Flexy\SystemBundle\Lib\Backend\BackendController;
use Flexy\SystemBundle\Entity\Locale;
use Flexy\SystemBundle\Form\Backend\LocaleType;

/**
 * Locale Controller
 */
class LocaleController extends BackendController
{
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
    }

    /**
     * Lists all locale entities.
     *
     * @return Response
     */
    public function listAction()
    {
        $locales = $this->getEm()->getRepository('FlexySystemBundle:Locale')->findAll();

        return $this->render('FlexySystemBundle:Backend/Locale/Locale:list.html.twig', array(
            'locales' => $locales
        ));
    }

    /**
     * Displays a form to edit an existing locale entity or create a new one.
     *
     * @param integer $id      The id of the Locale to edit
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function editAction($id, Request $request)
    {
        /**
         * @var $locale Locale
         */
        $locale = $this->getEm()->getRepository('FlexySystemBundle:Locale')->find($id);

        if (false == $locale) {
            $locale = new Locale();
            $locale->setContainer($this->container);
        }

        $form = $this->createForm(new LocaleType(), $locale);

        if ('POST' == $request->getMethod()) {

            $form->submit($request);

            if ($form->isValid()) {

                $this->getEm()->persist($locale);
                $this->getEm()->flush();

                $this->addFlashSuccess($this->get('translator')->trans(
                    '%entity% has been saved.',
                    array('%entity%' => $locale))
                );

                if ($request->request->has('save')) {
                    return $this->redirect($this->generateUrl('flexy_system_backend_locale'));
                }

                return $this->redirect($this->generateUrl($locale->getRoute(), $locale->getRouteParams()));
            } else {
                $this->addFlashError('Some fields are invalid.');
            }
        }

        return $this->render('FlexySystemBundle:Backend/Locale/Locale:edit.html.twig', array(
            'locale' => $locale,
            'form' => $form->createView()
        ));
    }

    /**
     * Check if we can delete a Locale.
     *
     * @param Request $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function checkDeleteAction(Request $request, $id)
    {
        $locale = $this->getEm()->getRepository('FlexySystemBundle:Locale')->find($id);
        $output = $this->checkDeleteEntity($locale);

        return new JsonResponse($output);
    }

    /**
     * Delete a Locale entity.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $locale = $this->getEm()->getRepository('FlexySystemBundle:Locale')->find($id);
        $this->deleteEntity($locale);

        return $this->redirect($this->generateUrl('flexy_system_backend_locale'));
    }

}
