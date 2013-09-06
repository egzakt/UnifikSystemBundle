<?php

namespace Egzakt\SystemBundle\Form\Type;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Egzakt\SystemBundle\Form\ChoiceList\ORMSortedQueryBuilderLoader;
use Egzakt\SystemBundle\Lib\TreeEntityOrderer;
use Egzakt\SystemBundle\Lib\NavigationElementInterface;

/**
 * Class TreeChoiceType
 */
class TreeChoiceType extends AbstractType
{
    /**
     * @var TreeEntityOrderer
     */
    protected $treeEntityOrderer;

    /**
     * Constructor
     *
     * @param TreeEntityOrderer $treeEntityOrderer
     */
    public function __construct(TreeEntityOrderer $treeEntityOrderer)
    {
        $this->treeEntityOrderer = $treeEntityOrderer;
    }

    /**
     * Get Parent
     *
     * This field type is based on EntityType
     *
     * @return null|string
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return 'tree_choice';
    }

    /**
     * {@inheritdoc}
     *
     * The EntityChoiceList has been extended to sort choices
     *
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        // Set a default query_builder so our custom Loader is always used
        $queryBuilder = function (Options $options) {
            return $options['em']->getRepository($options['class'])->createQueryBuilder('e')->select('e');
        };

        // Set a custom Loader that will sort the entities if the option is set
        $type = $this;

        $loader = function (Options $options) use ($type, $queryBuilder) {
            return $type->getLoader($options['em'], $options['query_builder'], $options['class'], $options['automatic_sorting']);
        };

        // Replace the default options with these new ones
        $resolver->replaceDefaults(array(
            'query_builder' => $queryBuilder,
            'loader' => $loader
        ));

        // Add some custom default options
        $defaults = array(
            'automatic_sorting' => true,
            'add_select_all'    => true
        );

        $resolver->setDefaults($defaults);
    }

    /**
     * Return the default loader object
     *
     * @param ObjectManager $manager
     * @param $queryBuilder
     * @param $class
     * @param $automaticSorting
     *
     * @return ORMSortedQueryBuilderLoader
     */
    public function getLoader(ObjectManager $manager, $queryBuilder, $class, $automaticSorting)
    {
        return new ORMSortedQueryBuilderLoader(
            $queryBuilder,
            $manager,
            $class,
            $automaticSorting,
            $this->treeEntityOrderer
        );
    }

    /**
     * {@inheritdoc}
     *
     * Add a level property for all childrens to the main FormView
     *
     * In case of a multiple widget not expanded, buildViewBottomUp won't be called because
     * all different values are in the same widget
     *
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // Get the entities
        $entities = $view->vars['choices'];

        $levels = array();
        foreach ($entities as $id => $entity) {

            if (!$entity->data instanceof NavigationElementInterface) {
                throw new \Exception('Tree Choice elements must extend the NavigationElementInterface.');
            }

            $levels['level_id_' . $id] = $entity->data->getLevel();
        }

        $view->vars['levels'] = $levels;

        // For the Generic Label
        $view->vars['withColon'] = true;

        // Add the parameter to the view
        if ($options['add_select_all']) {
            $view->vars['add_select_all'] = true;
        }
    }

    /**
     * {@inheritdoc}
     *
     * Add a level property to all children FormView
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // Get the entity list
        $entityChoiceList = $view->vars['choices'];

        // For all childrens (FormView instances)
        // For example : 'sections' (root FormView) contains many checkboxes (children FormView)
        foreach ($view->children as $key => $childrenView) {
            if (array_key_exists($key, $entityChoiceList)) {
                $entity = $entityChoiceList[$key];

                if (!$entity->data instanceof NavigationElementInterface) {
                    throw new \Exception('Tree Choice elements must extend the NavigationElementInterface.');
                }

                // Set the level on the child FormView, a checkbox for exemple
                $childrenView->vars['level'] = $entity->data->getLevel();
            }
        }
    }

}
