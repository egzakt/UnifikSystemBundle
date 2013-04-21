<?php

namespace Egzakt\SystemBundle\Controller\Backend\Section;

use Egzakt\SystemBundle\Entity\Mapping;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Finder\Finder;

use Egzakt\SystemBundle\Lib\Backend\BaseController;
use Egzakt\SystemBundle\Entity\Section;
use Egzakt\SystemBundle\Form\Backend\RootSectionType;

/**
 * RootSection Controller
 */
class RootController extends BaseController
{
    /**
     * Lists all root sections by navigation
     *
     * @return Response
     */
    public function listAction()
    {
        $navigations = $this->getEm()->getRepository('EgzaktSystemBundle:Navigation')->findHaveSections();

        return $this->render('EgzaktSystemBundle:Backend/Section/Root:list.html.twig', array(
            'navigations' => $navigations
        ));
    }

    /**
     * Displays a form to edit an existing Section entity or create a new one.
     *
     * @param integer $id The id of the Section to edit
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function editAction($id, Request $request)
    {
        $entity = $this->getEm()->getRepository('EgzaktSystemBundle:Section')->find($id);

        if (false == $entity) {
            $entity = new Section();
            $entity->setContainer($this->container);
            $entity->setApp($this->getApp());
        }

        $form = $this->createForm(new RootSectionType(), $entity);

        if ('POST' == $request->getMethod()) {

            $form->bindRequest($request);

            if ($form->isValid()) {

                $this->getEm()->persist($entity);

                // On insert
                if (false == $id) {

		    $sectionBar = $this->getEm()->getRepository('EgzaktSystemBundle:Navigation')->findOneByName('_section_bar');
		    $sectionModuleBar = $this->getEm()->getRepository('EgzaktSystemBundle:Navigation')->findOneByName('_section_module_bar');

		    $mapping = new Mapping();
		    $mapping->setSection($entity);
		    $mapping->setApp($this->getApp());
		    $mapping->setType('route');
		    $mapping->setTarget('egzakt_system_backend_text');

		    $entity->addMapping($mapping);
                }

                $this->getEm()->flush();
                $this->invalidateRoutingCache();

                if ($request->request->has('save')) {
                    return $this->redirect($this->generateUrl('egzakt_system_backend_section_root'));
                }

                return $this->redirect($this->generateUrl('egzakt_system_backend_section_root_edit', array(
                    'id' => $entity->getId() ?: 0
                )));
            }
        }

        return $this->render('EgzaktSystemBundle:Backend/Section/Root:edit.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * Deletes a RootSection entity.
     *
     * @param integer $id The ID of the RootSection to delete
     *
     * @throws NotFoundHttpException
     *
     * @return Response|RedirectResponse
     */
    public function deleteAction($id)
    {
        $entity = $this->getEm()->getRepository('EgzaktBackendSectionBundle:Section')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Section entity.');
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

        return $this->redirect($this->generateUrl($this->getBundleName(), array()));
    }

    /**
     * Set order on RootSection entities.
     *
     * @return Response
     */
    public function orderAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {

            $i = 0;

            $repo = $this->getEm()->getRepository('EgzaktFrontendCoreBundle:SectionNavigation');
            $elements = explode(';', trim($this->getRequest()->request->get('elements'), ';'));

            // Get the navigation id
            preg_match('/_(.)*-/', $elements[0], $matches);
            $navigationId = $matches[1];

            foreach ($elements as $element) {

                $sectionId = preg_replace('/(.)*-/', '', $element);
                $entity = $repo->findOneBy(array('section' => $sectionId, 'navigation' => $navigationId));

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
