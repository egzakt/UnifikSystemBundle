<?php

namespace Egzakt\SystemBundle\Controller\Backend\Section;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Egzakt\SystemBundle\Lib\Backend\BaseController;;
use Egzakt\SystemBundle\Entity\Mapping;
use Egzakt\SystemBundle\Entity\NavigationRepository;
use Egzakt\SystemBundle\Entity\Section;
use Egzakt\SystemBundle\Entity\SectionRepository;
use Egzakt\SystemBundle\Form\Backend\SectionType;

/**
 * Section controller.
 *
 */
class SectionController extends BaseController
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

        $this->sectionRepository = $this->getEm()->getRepository('EgzaktSystemBundle:Section');
        $this->navigationRepository = $this->getEm()->getRepository('EgzaktSystemBundle:Navigation');

//        $this->getCore()->addNavigationElement($this->getSectionBundle());
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
     * @param integer $id The Section ID
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id)
    {
        $section = $this->getSection();
        $app = $this->getApp();

        $entity = $this->sectionRepository->find($id);

        if (!$entity) {
            $entity = new Section();
            $entity->setContainer($this->container);
            $entity->setParent($section);
            $entity->setApp($app);
        }

        $this->getCore()->addNavigationElement($entity);

        $form = $this->createForm(new SectionType(), $entity);

        if ('POST' === $request->getMethod()) {

            $form->bindRequest($request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($entity);

                // On insert
                if (false == $id) {

                    // Fetching default linked bundles
//                    $bundles = $em->getRepository('EgzaktBackendCoreBundle:Bundle')->findByParam(
//                        'automatically_linked',
//                        true
//                    );
//
//                    // If this section match the max level we remove it from the autolinked bundles
//                    $maxSectionLevel = $this->container->getParameter('egzakt_backend_section.max_level');
//                    $sectionLevel = ($this->getSection()->getLevel() + 1);
//
//                    if ($sectionLevel >= $maxSectionLevel) {
//                        foreach ($bundles as $key => $bundle) {
//                            if ('EgzaktBackendSectionBundle' == $bundle->getName()) {
//                                unset($bundles[$key]);
//                                break;
//                            }
//                        }
//                    }

                    $sectionModuleBar = $this->navigationRepository->findOneByName('_section_module_bar');

                    $mapping = new Mapping();
                    $mapping->setSection($entity);
                    $mapping->setApp($this->getApp());
                    $mapping->setType('route');
                    $mapping->setTarget('egzakt_system_backend_text');

                    $entity->addMapping($mapping);

                    $mapping = new Mapping();
                    $mapping->setSection($entity);
                    $mapping->setApp($this->getApp());
                    $mapping->setNavigation($sectionModuleBar);
                    $mapping->setType('render');
                    $mapping->setTarget('EgzaktSystemBundle:Backend/Text/Navigation:SectionModuleBar');

                    $entity->addMapping($mapping);

                    $mapping = new Mapping();
                    $mapping->setSection($entity);
                    $mapping->setApp($this->getApp());
                    $mapping->setNavigation($sectionModuleBar);
                    $mapping->setType('render');
                    $mapping->setTarget('EgzaktSystemBundle:Backend/Section/Navigation:SectionModuleBar');

                    $entity->addMapping($mapping);
                }

                $em->flush();

                $this->invalidateRoutingCache();

                if ($request->get('save')) {
                    return $this->redirect($this->generateUrl('egzakt_system_backend_section', array(
                        'section_id' => $section->getId()
                    )));
                }

                return $this->redirect($this->generateUrl('egzakt_system_backend_section_edit', array(
                    'id' => $entity->getId() ?: 0,
                    'section_id' => $section->getId()
                )));
            }
        }

        return $this->render('EgzaktSystemBundle:Backend/Section/Section:edit.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView()
        ));
    }

    /**
     * Deletes a Section entity.
     *
     * @param integer $id The ID of the Section to delete
     *
     * @throws NotFoundHttpException
     *
     * @return Response|RedirectResponse
     */
    public function deleteAction($id)
    {
        $entity = $this->getEm()->getRepository($this->getBundleName() . ':Section')->find($id);

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

        return $this->redirect($this->generateUrl($this->getBundleName(), array('section_id' => $this->getSection()->getId())));
    }


    /**
     * Set order on a Section entity.
     *
     * @return Response
     */
    public function orderAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {

            $i = 0;

            /** @var \Doctrine\ORM\EntityManager $em  */
            $em = $this->getDoctrine()->getEntityManager();
            $repo = $em->getRepository($this->getBundleName() . ':Section');

            $elements = explode(';', trim($this->getRequest()->request->get('elements'), ';'));

            foreach ($elements as $element) {

                $element = explode('_', $element);
                /** @var \Egzakt\Backend\SectionBundle\Entity\Section $entity  */
                $entity = $repo->find($element[1]);

                if ($entity) {
                    $entity->setOrdering(++$i);
                    $em->persist($entity);
                    $em->flush();
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

    /**
     * @deprecated
     *
     * Display all sections into a tree with checkboxes
     * (Called with a twig render from an entity's edit form)
     *
     * @param Doctrine\ORM\PersistentCollection $currentSections The current sections of the entity
     * @param Symfony\Component\Form\FormView   $form
     * @param int                               $maxLevel Max level of section children to display
     *
     * @return Response
     */
    public function formChoicesTreeAction($currentSections, $form, $maxLevel)
    {
        // TODO : waiting for the new query to get sections with children
        $sections = $this->getEm()->getRepository('EgzaktBackendSectionBundle:Section')->findBy(
            array(
                'app' => $this->getApp()->getId(),
                'parent' => null
            ),
            array('ordering' => 'ASC')
        );

        // Transform the current sections into an ids list
        $currentSectionsIds = array();

        foreach($currentSections as $section) {
            $currentSectionsIds[] = $section->getId();
        }

        return $this->render('EgzaktBackendSectionBundle:Section:form_choices_tree.html.twig', array(
            'currentSectionsIds' => $currentSectionsIds,
            'form' => $form,
            'sections' => $sections,
            'maxLevel' => $maxLevel
        ));
    }
}
