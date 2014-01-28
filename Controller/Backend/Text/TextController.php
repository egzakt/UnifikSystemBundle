<?php

namespace Unifik\SystemBundle\Controller\Backend\Text;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Unifik\SystemBundle\Lib\Backend\BackendController;
use Unifik\SystemBundle\Entity\Text;
use Unifik\SystemBundle\Form\Backend\TextMainType;
use Unifik\SystemBundle\Form\Backend\TextStaticType;

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

        $this->createAndPushNavigationElement('Text list', 'unifik_system_backend_text');
    }

    /**
     * Lists all Text entities.
     *
     * @return Response
     */
    public function indexAction()
    {
        $section = $this->getSection();

        $mainEntities = $this->getEm()->getRepository('UnifikSystemBundle:Text')->findBy(array(
            'section' => $section->getId(),
            'static' => false
        ), array(
            'ordering' => 'ASC'
        ));

        $staticEntities = $this->getEm()->getRepository('UnifikSystemBundle:Text')->findBy(array(
            'section' => $section->getId(),
            'static' => true
        ), array(
            'ordering' => 'ASC'
        ));

        return $this->render('UnifikSystemBundle:Backend/Text/Text:list.html.twig', array(
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

        $text = $this->getEm()->getRepository('UnifikSystemBundle:Text')->find($id);

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

                $this->get('unifik_system.router_invalidator')->invalidate();

                $this->addFlashSuccess('The Text has been saved.');

                if ($request->request->has('save')) {
                    return $this->redirect($this->generateUrl('unifik_system_backend_text'));
                }

                return $this->redirect($this->generateUrl('unifik_system_backend_text_edit', array(
                    'id' => $text->getId() ?: 0
                )));
            } else {
                $this->addFlashError('Some fields are invalid.');
            }
        }

        return $this->render('UnifikSystemBundle:Backend/Text/Text:edit.html.twig', array(
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
        $text = $this->getEm()->getRepository('UnifikSystemBundle:Text')->find($id);
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
        $text = $this->getEm()->getRepository('UnifikSystemBundle:Text')->find($id);
        $this->deleteEntity($text);
        $this->get('unifik_system.router_invalidator')->invalidate();

        return $this->redirect($this->generateUrl('unifik_system_backend_text'));
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
        $textRepo = $this->getEm()->getRepository('UnifikSystemBundle:Text');
        $this->orderEntities($request, $textRepo);
        $this->get('unifik_system.router_invalidator')->invalidate();

        return new Response('');
    }

}
