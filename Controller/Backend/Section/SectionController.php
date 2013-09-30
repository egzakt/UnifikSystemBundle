<?php

namespace Egzakt\SystemBundle\Controller\Backend\Section;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Egzakt\SystemBundle\Lib\Backend\BackendController;
use Egzakt\SystemBundle\Entity\Mapping;
use Egzakt\SystemBundle\Entity\NavigationRepository;
use Egzakt\SystemBundle\Entity\Section;
use Egzakt\SystemBundle\Entity\SectionRepository;
use Egzakt\SystemBundle\Form\Backend\SectionType;

/**
 * Section controller.
 *
 */
class SectionController extends BackendController
{
    /**
     * @var SectionRepository
     */
    protected $sectionRepository;

    /**
     * @var NavigationRepository
     */
    protected $navigationRepository;

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

        $this->sectionRepository = $this->getEm()->getRepository('EgzaktSystemBundle:Section');
        $this->navigationRepository = $this->getEm()->getRepository('EgzaktSystemBundle:Navigation');
    }

    /**
     * Lists all Section entities.
     *
     * @return Response
     */
    public function indexAction()
    {
        $entities = $this->sectionRepository->findBy(
            array('parent' => $this->getSection()->getId()),
            array('ordering' => 'ASC')
        );

        return $this->render('EgzaktSystemBundle:Backend/Section/Section:list.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Displays a form to edit an existing Text entity.
     *
     * @param Request $request
     * @param integer $id      The Section ID
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $section = $this->getSection();
        $app = $this->getApp();

        $entity = $this->sectionRepository->find($id);

        if (!$entity) {
            $entity = $this->initEntity(new Section());
            $entity->setParent($section);
            $entity->setApp($app);
        }

        $this->getCore()->addNavigationElement($entity);

        $form = $this->createForm(new SectionType(), $entity, array('current_section' => $entity, 'managed_app' => $this->getApp()));

        if ('POST' === $request->getMethod()) {

            $form->submit($request);

            if ($form->isValid()) {

                $this->getEm()->persist($entity);

                // On insert
                if (false == $id) {

                    $sectionModuleBar = $this->navigationRepository->find(NavigationRepository::SECTION_MODULE_BAR_ID);

                    $app = $this->getEm()->getRepository('EgzaktSystemBundle:App')->findOneByName('backend');

                    $mapping = new Mapping();
                    $mapping->setSection($entity);
                    $mapping->setApp($app);
                    $mapping->setType('route');
                    $mapping->setTarget('egzakt_system_backend_text');

                    $entity->addMapping($mapping);

                    $mapping = new Mapping();
                    $mapping->setSection($entity);
                    $mapping->setApp($app);
                    $mapping->setNavigation($sectionModuleBar);
                    $mapping->setType('render');
                    $mapping->setTarget('EgzaktSystemBundle:Backend/Text/Navigation:SectionModuleBar');

                    $entity->addMapping($mapping);

                    $mapping = new Mapping();
                    $mapping->setSection($entity);
                    $mapping->setApp($app);
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

                $this->addFlashSuccess($this->get('translator')->trans('%entity% has been saved.', array('%entity%' => $entity)));

                if ($request->get('save')) {
                    return $this->redirect($this->generateUrl('egzakt_system_backend_section'));
                }

                return $this->redirect($this->generateUrl('egzakt_system_backend_section_edit', array(
                    'id' => $entity->getId() ?: 0
                )));
            } else {
                $this->addFlashError('Some fields are invalid.');
            }
        }

        return $this->render('EgzaktSystemBundle:Backend/Section/Section:edit.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * Check if we can delete a Section.
     *
     * @param Request $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function checkDeleteAction(Request $request, $id)
    {
        $section = $this->sectionRepository->find($id);
        $output = $this->checkDeleteEntity($section);

        return new JsonResponse($output);
    }

    /**
     * Deletes a Section entity.
     *
     * @param Request $request
     * @param integer $id      The ID of the Section to delete
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $section = $this->sectionRepository->find($id);
        $this->deleteEntity($section);
        $this->get('egzakt_system.router_invalidator')->invalidate();

        return $this->redirect($this->generateUrl('egzakt_system_backend_section'));
    }

    /**
     * Set order on a Section entity.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function orderAction(Request $request)
    {
        $this->orderEntities($request, $this->sectionRepository);
        $this->get('egzakt_system.router_invalidator')->invalidate();

        return new Response('');
    }

}
