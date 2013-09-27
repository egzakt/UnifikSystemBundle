<?php

namespace Egzakt\SystemBundle\Controller\Backend\Locale;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Egzakt\SystemBundle\Lib\Backend\BaseController;
use Egzakt\SystemBundle\Entity\Locale;
use Egzakt\SystemBundle\Form\Backend\LocaleType;

/**
 * Locale Controller
 */
class LocaleController extends BaseController
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
        $locales = $this->getEm()->getRepository('EgzaktSystemBundle:Locale')->findAll();

        return $this->render('EgzaktSystemBundle:Backend/Locale/Locale:list.html.twig', array(
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
        $locale = $this->getEm()->getRepository('EgzaktSystemBundle:Locale')->find($id);

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
                    return $this->redirect($this->generateUrl('egzakt_system_backend_locale'));
                }

                return $this->redirect($this->generateUrl($locale->getRoute(), $locale->getRouteParams()));
            } else {
                $this->addFlashError('Some fields are invalid.');
            }
        }

        return $this->render('EgzaktSystemBundle:Backend/Locale/Locale:edit.html.twig', array(
            'locale' => $locale,
            'form' => $form->createView()
        ));
    }

    /**
     * Check if we can delete a Locale.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function checkDeleteAction(Request $request, $id)
    {
        $entity = $this->getEm()->getRepository('EgzaktSystemBundle:Locale')->find($id);

        if (null === $entity) {
            throw new NotFoundHttpException();
        }

        $result = $this->checkDeletable($entity);
        $output = $result->toArray();
        $output['template'] = $this->renderView('EgzaktSystemBundle:Backend/Core:delete_message.html.twig',
            array(
                'entity' => $entity,
                'result' => $result
            )
        );

        return new JsonResponse($output);

    }

    /**
     * Delete a Locale entity.
     *
     * @param Request $request
     * @param int     $id
     *
     * @return RedirectResponse|Response
     *
     * @throws NotFoundHttpException
     */
    public function deleteAction(Request $request, $id)
    {
        $locale = $this->getEm()->getRepository('EgzaktSystemBundle:Locale')->find($id);

        if (!$locale) {
            throw $this->createNotFoundException('Unable to find a locale entity using id "' . $id . '".');
        }

        $result = $this->checkDeletable($locale);
        if ($result->isSuccess()) {
            $this->addFlashSuccess($this->get('translator')->trans(
                '%entity% has been deleted.',
                array('%entity%' => $locale)
            ));

            $this->getEm()->remove($locale);
            $this->getEm()->flush();
        } else {
            $this->addFlashError($result->getErrors());
        }

        return $this->redirect($this->generateUrl('egzakt_system_backend_locale'));
    }

}
