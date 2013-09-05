<?php

namespace Egzakt\SystemBundle\Controller\Backend\Locale;

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
     * @param integer $id The id of the Locale to edit
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

                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans(
                    '%entity% has been updated.',
                    array('%entity%' => $locale))
                );

                if ($request->request->has('save')) {
                    return $this->redirect($this->generateUrl('egzakt_system_backend_locale'));
                }

                return $this->redirect($this->generateUrl($locale->getRoute(), $locale->getRouteParams()));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Some fields are invalid.');
            }
        }

        return $this->render('EgzaktSystemBundle:Backend/Locale/Locale:edit.html.twig', array(
            'locale' => $locale,
            'form' => $form->createView()
        ));
    }

    /**
     * Delete a Locale entity.
     *
     * @param Request $request
     * @param int $id
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

        if ($request->get('message')) {
            $template = $this->renderView('EgzaktSystemBundle:Backend/Core:delete_message.html.twig', array(
                'entity' => $locale
            ));

            return new Response(json_encode(array(
                'template' => $template,
                'isDeletable' => $locale->isDeletable()
            )));
        }

        // Call the translator before we flush the entity so we can have the real __toString()
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans(
            '%entity% has been deleted.',
            array('%entity%' => $locale != '' ? $locale : $locale->getEntityName()))
        );

        $this->getEm()->remove($locale);
        $this->getEm()->flush();

        return $this->redirect($this->generateUrl('egzakt_system_backend_locale'));
    }

}
