<?php

namespace Egzakt\SystemBundle\Lib;

/**
 * Class TreeEntityOrderer
 */
class TreeEntityOrderer
{
    /**
     * Sort Entities
     *
     * Sort the entities to a parent/child tree view
     *
     * @param $entities
     *
     * @return array
     */
    public function sortEntities($entities)
    {
        $tree = array();

        foreach ($entities as $entity) {

            $entity->setChildren(null);
            $tree[$entity->getId()] = $entity;
        }

        foreach ($tree as $entity) {

            if ($parent = $entity->getParent()) {
                if (isset($tree[$parent->getId()])) {
                    $tree[$parent->getId()]->addChildren($entity);
                }
            }
        }

        foreach ($tree as $entityId => $entity) {

            if ($entity->getParent()) {
                unset($tree[$entityId]);
            }
        }

        $flatTree = $this->flatenizeChildrens($tree, array());

        // Remove the childrens, otherwise the childrens must be a PersistentCollection
        foreach ($flatTree as $entity) {
            $entity->setChildren(null);
        }

        return $flatTree;
    }

    /**
     * Flatenize Childrens
     *
     * Bring a parent/children tree to a flat array
     *
     * @param $entities
     * @param $flatTree
     *
     * @return array
     */
    protected function flatenizeChildrens($entities, $flatTree)
    {
        foreach ($entities as $entity) {
            $flatTree[] = $entity;
            if ($entity->hasChildren()) {
                $flatTree = $this->flatenizeChildrens($entity->getChildren(), $flatTree);
            }
        }

        return $flatTree;
    }

}
