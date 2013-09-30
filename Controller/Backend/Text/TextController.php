<?php

namespace Egzakt\SystemBundle\Controller\Backend\Text;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Egzakt\SystemBundle\Lib\Backend\BackendController;
use Egzakt\SystemBundle\Entity\Text;
use Egzakt\SystemBundle\Form\Backend\TextMainType;
use Egzakt\SystemBundle\Form\Backend\TextStaticType;

/**
 * Text controller.
 *
 * @throws NotFoundHttpException
 */
class TextController extends BackendController
{
    /**
     * Init
     */
    public function init()
    {
        parent::init();

        $this->createAndPushNavigationElement('Text list', 'egzakt_system_backend_text');
    }

    /**
     * Lists all Text entities.
     *
     * @return Response
     */
    public function indexAction()
    {
        $section = $this->getSection();

        $mainEntities = $this->getEm()->getRepository('EgzaktSystemBundle:Text')->findBy(array(
            'section' => $section->getId(),
            'static' => false
        ), array(
            'ordering' => 'ASC'
        ));

        $staticEntities = $this->getEm()->getRepository('EgzaktSystemBundle:Text')->findBy(array(
            'section' => $section->getId(),
            'static' => true
        ), array(
            'ordering' => 'ASC'
        ));

        return $this->render('EgzaktSystemBundle:Backend/Text/Text:list.html.twig', array(
            'mainEntities' => $mainEntities,
            'staticEntities' => $staticEntities,
            'truncateLength' => 100
        ));
    }

    /**
     * Displays a form to edit or create a Text entity.
     *
     * @param Request $request
     * @param integer $id      The ID
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $section = $this->getSection();

        $text = $this->getEm()->getRepository('EgzaktSystemBundle:Text')->find($id);

        if (false == $text) {
            $text = $this->initEntity(new Text());
            $text->setSection($section);
        }

        $this->getCore()->addNavigationElement($text);

        if ($text->isStatic()) {
            $formType = new TextStaticType();
        } else {
            $formType = new TextMainType();
        }

        $form = $this->createForm($formType, $text);

        if ('POST' == $request->getMethod()) {

            $form->submit($request);

            if ($form->isValid()) {

                $em = $this->getEm();
                $em->persist($text);
                $em->flush();

                $this->get('egzakt_system.router_invalidator')->invalidate();

                $this->addFlashSuccess('The Text has been saved.');

                if ($request->request->has('save')) {
                    return $this->redirect($this->generateUrl('egzakt_system_backend_text'));
                }

                return $this->redirect($this->generateUrl('egzakt_system_backend_text_edit', array(
                    'id' => $text->getId() ?: 0
                )));
            } else {
                $this->addFlashError('Some fields are invalid.');
            }
        }

        return $this->render('EgzaktSystemBundle:Backend/Text/Text:edit.html.twig', array(
            'text' => $text,
            'form' => $form->createView(),
        ));
    }

    /**
     * Check if we can delete a Text.
     *
     * @param Request $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function checkDeleteAction(Request $request, $id)
    {
        $text = $this->getEm()->getRepository('EgzaktSystemBundle:Text')->find($id);
        $output = $this->checkDeleteEntity($text);

        return new JsonResponse($output);

    }

    /**
     * Delete a Text.
     *
     * @param $id
     * @return RedirectResponse
     * @throws NotFoundHttpException
     */
    public function deleteAction($id)
    {
        $text = $this->getEm()->getRepository('EgzaktSystemBundle:Text')->find($id);
        $this->deleteEntity($text);
        $this->get('egzakt_system.router_invalidator')->invalidate();

        return $this->redirect($this->generateUrl('egzakt_system_backend_text'));
    }

    /**
     * Set order on a Text entity.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function orderAction(Request $request)
    {
        $textRepo = $this->getEm()->getRepository('EgzaktSystemBundle:Text');
        $this->orderEntities($request, $textRepo);
        $this->get('egzakt_system.router_invalidator')->invalidate();

        return new Response('');
    }

}
