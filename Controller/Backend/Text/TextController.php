<?php

namespace Egzakt\SystemBundle\Controller\Backend\Text;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Egzakt\SystemBundle\Lib\Backend\BaseController;
use Egzakt\Backend\TextBundle\Form\TextMainType;
use Egzakt\Backend\TextBundle\Form\TextStaticType;

/**
 * Text controller.
 *
 * @throws NotFoundHttpException
 */
class TextController extends BaseController
{
    /**
     * Init
     */
    public function init()
    {
        parent::init();

//        $this->getCore()->addNavigationElement($this->getSectionBundle());
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
            'section' => $section,
            'truncateLength' => 100
        ));
    }

    /**
     * Displays a form to edit an existing Text entity.
     *
     * @param integer $id The ID
     *
     * @return RedirectResponse|Response
     */
    public function editAction($id)
    {
        $section = $this->getSection();

        $entity = $this->getEm()->getRepository($this->getBundleName() . ':Text')->find($id);

        if (false == $entity) {
            $entity = new Text();
            $entity->setContainer($this->container);
            $entity->setSection($section);
        }

        $this->getCore()->addNavigationElement($entity);
        $request = $this->getRequest();

        if ($entity->isStatic()) {
            $formType = new TextStaticType();
        } else {
            $formType = new TextMainType();
        }

        $form = $this->createForm($formType, $entity);

        if ('POST' == $request->getMethod()) {

            $form->bindRequest($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($entity);
                $em->flush();

                $this->invalidateRoutingCache();

                if ($request->request->has('save')) {

                    return $this->redirect($this->generateUrl($this->getBundleName(), array(
                        'section_id' => $section->getId()
                    )));
                }

                return $this->redirect($this->generateUrl($this->getBundleName() . '_edit', array(
                    'id' => $entity->getId() ?: 0,
                    'section_id' => $section->getId()
                )));
            }
        }

        return $this->render($this->getBundleName() . ':Text:edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $form->createView(),
            'section' => $section,
            'truncateLength' => $this->getSectionBundle()->getParam('list_truncate_length')
        ));
    }

    /**
     * Deletes a Text entity.
     *
     * @param integer $id The Id of the text to delete
     *
     * @throws \Symfony\Bundle\FrameworkBundle\Controller\NotFoundHttpException
     *
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {
        $entity = $this->getEm()->getRepository($this->getBundleName() . ':Text')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Text entity.');
        }

        if ($this->get('request')->get('message')) {
            $template = $this->renderView('EgzaktBackendCoreBundle:Core:delete_message.html.twig', array(
                'entity' => $entity,
                'truncateLength' => $this->getSectionBundle()->getParam('breadcrumbs_truncate_length')
            ));

            return new Response(json_encode(array(
                'template' => $template,
                'isDeletable' => $entity->isDeletable()
            )));
        }

        $this->getEm()->remove($entity);
        $this->getEm()->flush();

        $this->invalidateRoutingCache();

        return $this->redirect($this->generateUrl($this->getBundleName(), array('section_id' => $this->getSection()->getId())));
    }


    /**
     * Set order on a BloxTexte entity.
     *
     * @return Response
     */
    public function orderAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {

            $i = 0;

            $repo = $this->getEm()->getRepository($this->getBundleName() . ':Text');

            $elements = explode(';', trim($this->getRequest()->request->get('elements'), ';'));

            foreach ($elements as $element) {

                $element = explode('_', $element);
                $entity = $repo->find($element[1]);

                if ($entity) {
                    $entity->setOrdering(++$i);
                    $this->getEm()->persist($entity);
                    $this->getEm()->flush();
                }
            }

            $this->invalidateRoutingCache();
        }

        return new Response('');
    }

    /**
     * Invalidate Routing Cache
     */
    private function invalidateRoutingCache()
    {
        $finder = new Finder();
        $cacheDir = $this->container->getParameter('kernel.cache_dir');

        foreach ($finder->files()->name('/(.*)Url(Matcher|Generator)(.*)/')->in($cacheDir) as $file) {
            unlink($file);
        }
    }

}
