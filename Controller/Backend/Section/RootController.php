<?php

namespace Egzakt\SystemBundle\Controller\Backend\Section;

use Egzakt\SystemBundle\Entity\Mapping;
use Egzakt\SystemBundle\Entity\NavigationRepository;
use Egzakt\SystemBundle\Entity\SectionRepository;
use Egzakt\SystemBundle\Lib\DeletableResult;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Egzakt\SystemBundle\Lib\Backend\BaseController;
use Egzakt\SystemBundle\Entity\Section;
use Egzakt\SystemBundle\Form\Backend\RootSectionType;

/**
 * RootSection Controller
 */
class RootController extends BaseController
{
    /**
     * @var NavigationRepository
     */
    protected $navigationRepository;

    /**
     * @var SectionRepository
     */
    protected $sectionRepository;

    /**
     * @var AppRepository
     */
    protected $appRepository;

    /**
     * Init
     */
    public function init()
    {
        parent::init();

        $this->createAndPushNavigationElement('Sections', 'egzakt_system_backend_section_root', array(
            'appSlug' => $this->getApp()->getSlug()
        ));

        $this->navigationRepository = $this->getEm()->getRepository('EgzaktSystemBundle:Navigation');
        $this->sectionRepository = $this->getEm()->getRepository('EgzaktSystemBundle:Section');
        $this->appRepository = $this->getEm()->getRepository('EgzaktSystemBundle:App');
    }

    /**
     * Lists all root sections by navigation
     *
     * @return Response
     */
    public function listAction()
    {
        $navigations = $this->navigationRepository->findHaveSections($this->getApp()->getId());
        $withoutNavigation = $this->sectionRepository->findRootsWithoutNavigation($this->getApp()->getId());

        return $this->render('EgzaktSystemBundle:Backend/Section/Root:list.html.twig', array(
            'navigations' => $navigations,
            'withoutNavigation' => $withoutNavigation,
            'managedApp' => $this->getApp()
        ));
    }

    /**
     * Displays a form to edit an existing Section entity or create a new one.
     *
     * @param integer $id      The id of the Section to edit
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function editAction($id, Request $request)
    {
        $entity = $this->sectionRepository->find($id);

        if (false == $entity) {
            $entity = $this->initEntity(new Section());
            $entity->setApp($this->getApp());
        }

        $this->pushNavigationElement($entity);

        $form = $this->createForm(new RootSectionType(), $entity, array('current_section' => $entity, 'managed_app' => $this->getApp()));

        if ('POST' == $request->getMethod()) {

            $form->submit($request);

            if ($form->isValid()) {

                $this->getEm()->persist($entity);

                // On insert
                if (false == $id) {

                    $sectionModuleBar = $this->navigationRepository->find(NavigationRepository::SECTION_MODULE_BAR_ID);
                    $backendApp = $this->appRepository->find(1);

                    $mapping = new Mapping();
                    $mapping->setSection($entity);
                    $mapping->setApp($backendApp);
                    $mapping->setType('route');
                    $mapping->setTarget('egzakt_system_backend_text');

                    $entity->addMapping($mapping);

                    $mapping = new Mapping();
                    $mapping->setSection($entity);
                    $mapping->setApp($backendApp);
                    $mapping->setNavigation($sectionModuleBar);
                    $mapping->setType('render');
                    $mapping->setTarget('EgzaktSystemBundle:Backend/Text/Navigation:SectionModuleBar');

                    $entity->addMapping($mapping);

                    $mapping = new Mapping();
                    $mapping->setSection($entity);
                    $mapping->setApp($backendApp);
                    $mapping->setNavigation($sectionModuleBar);
                    $mapping->setType('render');
                    $mapping->setTarget('EgzaktSystemBundle:Backend/Section/Navigation:SectionModuleBar');

                    $entity->addMapping($mapping);

                    // Frontend mapping
                    $mapping = new Mapping();
                    $mapping->setSection($entity);
                    $mapping->setApp($this->getApp());
                    $mapping->setType('route');
                    $mapping->setTarget('egzakt_system_frontend_text');

                    $entity->addMapping($mapping);
                }

                $this->getEm()->flush();
                $this->get('egzakt_system.router_invalidator')->invalidate();

                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans(
                    '%entity% has been updated.',
                    array('%entity%' => $entity))
                );

                if ($request->request->has('save')) {
                    return $this->redirect($this->generateUrl('egzakt_system_backend_section_root', array('appSlug' => $this->getApp()->getSlug())));
                }

                return $this->redirect($this->generateUrl('egzakt_system_backend_section_root_edit', array(
                    'id' => $entity->getId() ? : 0,
                    'appSlug' => $this->getApp()->getSlug()
                )));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Some fields are invalid.');
            }
        }

        return $this->render('EgzaktSystemBundle:Backend/Section/Root:edit.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'managedApp' => $this->getApp()
        ));
    }

    /**
     * Check if we can delete a Section.
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function checkDeleteAction(Request $request, $id)
    {

        $entity = $this->sectionRepository->find($id);

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
     * Deletes a RootSection entity.
     *
     * @param Request $request T
     *
     * @param integer $id The ID of the RootSection to delete
     *
     * @throws \Exception
     * @throws NotFoundHttpException
     *
     * @return Response|RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $section = $this->sectionRepository->find($id);

        if (!$section) {
            throw $this->createNotFoundException('Unable to find Section entity.');
        }

        $result = $this->checkDeletable($section);
        if ($result->isSuccess()) {
            $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans(
                '%entity% has been deleted.',
                array('%entity%' => $section->getName() != '' ? $section->getName() : $section->getEntityName()))
            );

            $this->getEm()->remove($section);
            $this->getEm()->flush();

            $this->get('egzakt_system.router_invalidator')->invalidate();
        } else {
            $this->addFlash('error', $result->getErrors());
        }

        return $this->redirect($this->generateUrl('egzakt_system_backend_section_root', array('appSlug' => $this->getApp()->getSlug())));
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
            $elements = explode(';', trim($this->getRequest()->request->get('elements'), ';'));

            // Get the navigation id
            preg_match('/_(.)*-/', $elements[0], $matches);
            $navigationId = $matches[1];

            foreach ($elements as $element) {

                $sectionId = preg_replace('/(.)*-/', '', $element);
                $entity = $this->getEm()->getRepository('EgzaktSystemBundle:SectionNavigation')->findOneBy(array('section' => $sectionId, 'navigation' => $navigationId));

                if ($entity) {
                    $entity->setOrdering(++$i);
                    $this->getEm()->persist($entity);
                    $this->getEm()->flush();
                }
            }

            $this->get('egzakt_system.router_invalidator')->invalidate();
        }

        return new Response('');
    }

}
