<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Flexy\SystemBundle\Generator\Tools\Export\Driver;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\Export\Driver\AbstractExporter;

/**
 * ClassMetadata exporter for Doctrine YAML mapping files
 *
 *
 * @link    www.doctrine-project.org
 * @since   2.0
 * @author  Jonathan Wage <jonwage@gmail.com>
 */
class YamlExporter extends AbstractExporter
{
    protected $_extension = '.dcm.yml';

    /**
     * Converts a single ClassMetadata instance to the exported format
     * and returns it
     *
     * TODO: Should this code be pulled out in to a toArray() method in ClassMetadata
     *
     * @param ClassMetadataInfo $metadata
     * @return mixed $exported
     */
    public function exportClassMetadata(ClassMetadataInfo $metadata, $indent = 4)
    {
        $array = array();

        if ($metadata->isMappedSuperclass) {
            $array['type'] = 'mappedSuperclass';
        } else {
            $array['type'] = 'entity';
        }

        if (isset($metadata->table['name'])) {
            $array['table'] = $metadata->table['name'];
        }

        if (isset($metadata->table['schema'])) {
            $array['schema'] = $metadata->table['schema'];
        }

        $inheritanceType = $metadata->inheritanceType;
        if ($inheritanceType !== ClassMetadataInfo::INHERITANCE_TYPE_NONE) {
            $array['inheritanceType'] = $this->_getInheritanceTypeString($inheritanceType);
        }

        if ($column = $metadata->discriminatorColumn) {
            $array['discriminatorColumn'] = $column;
        }

        if ($map = $metadata->discriminatorMap) {
            $array['discriminatorMap'] = $map;
        }

        if ($metadata->changeTrackingPolicy !== ClassMetadataInfo::CHANGETRACKING_DEFERRED_IMPLICIT) {
            $array['changeTrackingPolicy'] = $this->_getChangeTrackingPolicyString($metadata->changeTrackingPolicy);
        }

        if (isset($metadata->table['indexes'])) {
            $array['indexes'] = $metadata->table['indexes'];
        }

        if ($metadata->customRepositoryClassName) {
            $array['repositoryClass'] = $metadata->customRepositoryClassName;
        }

        if (isset($metadata->table['uniqueConstraints'])) {
            $array['uniqueConstraints'] = $metadata->table['uniqueConstraints'];
        }

        $fieldMappings = $metadata->fieldMappings;

        $ids = array();
        foreach ($fieldMappings as $name => $fieldMapping) {
            $fieldMapping['column'] = $fieldMapping['columnName'];
            unset(
                $fieldMapping['columnName'],
                $fieldMapping['fieldName']
            );

            if ($fieldMapping['column'] == $name) {
                unset($fieldMapping['column']);
            }

            if (isset($fieldMapping['id']) && $fieldMapping['id']) {
                $ids[$name] = $fieldMapping;
                unset($fieldMappings[$name]);
                continue;
            }

            $fieldMappings[$name] = $fieldMapping;
        }

        if ( ! $metadata->isIdentifierComposite && $idGeneratorType = $this->_getIdGeneratorTypeString($metadata->generatorType)) {
            $ids[$metadata->getSingleIdentifierFieldName()]['generator']['strategy'] = $idGeneratorType;
        }

        if ($ids) {
            $array['fields'] = $ids;
        }

        if ($fieldMappings) {
            if ( ! isset($array['fields'])) {
                $array['fields'] = array();
            }
            $array['fields'] = array_merge($array['fields'], $fieldMappings);
        }

        foreach ($metadata->associationMappings as $name => $associationMapping) {
            $cascade = array();
            if (isset($associationMapping['isCascadeRemove']) && $associationMapping['isCascadeRemove']) {
                $cascade[] = 'remove';
            }
            if (isset($associationMapping['isCascadePersist']) && $associationMapping['isCascadePersist']) {
                $cascade[] = 'persist';
            }
            if (isset($associationMapping['isCascadeRefresh']) && $associationMapping['isCascadeRefresh']) {
                $cascade[] = 'refresh';
            }
            if (isset($associationMapping['isCascadeMerge']) && $associationMapping['isCascadeMerge']) {
                $cascade[] = 'merge';
            }
            if (isset($associationMapping['isCascadeDetach']) && $associationMapping['isCascadeDetach']) {
                $cascade[] = 'detach';
            }
            if (count($cascade) === 5) {
                $cascade = array('all');
            }
            $associationMappingArray = array(
                'targetEntity' => $associationMapping['targetEntity'],
            );

            if (count($cascade) > 0) {
                $associationMappingArray['cascade'] = $cascade;
            }

            if ($associationMapping['type'] & ClassMetadataInfo::TO_ONE) {
                $joinColumns = $associationMapping['joinColumns'];
                $newJoinColumns = array();
                foreach ($joinColumns as $joinColumn) {
                    $newJoinColumns[$joinColumn['name']]['referencedColumnName'] = $joinColumn['referencedColumnName'];
                    if (isset($joinColumn['onDelete'])) {
                        $newJoinColumns[$joinColumn['name']]['onDelete'] = $joinColumn['onDelete'];
                    }
                }

                $oneToOneMappingArray = array();

                $oneToOneMappingArray['joinColumns'] = $newJoinColumns;

                if (isset($associationMapping['mappedBy'])) {
                    $oneToOneMappingArray['mappedBy'] = $associationMapping['mappedBy'];
                }
                if (isset($associationMapping['inversedBy'])) {
                    $oneToOneMappingArray['inversedBy'] = $associationMapping['inversedBy'];
                }
                if (isset($associationMapping['orphanRemoval'])) {
                    $oneToOneMappingArray['orphanRemoval'] = $associationMapping['orphanRemoval'];
                }

                $associationMappingArray = array_merge($associationMappingArray, $oneToOneMappingArray);

                if ($associationMapping['type'] & ClassMetadataInfo::ONE_TO_ONE) {
                    $array['oneToOne'][$name] = $associationMappingArray;
                } else {
                    $array['manyToOne'][$name] = $associationMappingArray;
                }

            } else if ($associationMapping['type'] == ClassMetadataInfo::ONE_TO_MANY) {

                $oneToManyMappingArray = array();

                if (isset($associationMapping['mappedBy'])) {
                    $oneToManyMappingArray['mappedBy'] = $associationMapping['mappedBy'];
                }
                if (isset($associationMapping['inversedBy'])) {
                    $oneToManyMappingArray['inversedBy'] = $associationMapping['inversedBy'];
                }
                if (isset($associationMapping['orphanRemoval'])) {
                    $oneToManyMappingArray['orphanRemoval'] = $associationMapping['orphanRemoval'];
                }
                if (isset($associationMapping['orderBy'])) {
                    $oneToManyMappingArray['orderBy'] = $associationMapping['orderBy'];
                }

                $associationMappingArray = array_merge($associationMappingArray, $oneToManyMappingArray);
                $array['oneToMany'][$name] = $associationMappingArray;
            } else if ($associationMapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {

                $manyToManyMappingArray = array();

                if (isset($associationMapping['mappedBy'])) {
                    $manyToManyMappingArray['mappedBy'] = $associationMapping['mappedBy'];
                }
                if (isset($associationMapping['inversedBy'])) {
                    $manyToManyMappingArray['inversedBy'] = $associationMapping['inversedBy'];
                }
                if (isset($associationMapping['joinTable'])) {
                    $manyToManyMappingArray['joinTable'] = $associationMapping['joinTable'];
                }
                if (isset($associationMapping['orderBy'])) {
                    $manyToManyMappingArray['orderBy'] = $associationMapping['orderBy'];
                }

                $associationMappingArray = array_merge($associationMappingArray, $manyToManyMappingArray);
                $array['manyToMany'][$name] = $associationMappingArray;
            }
        }
        if (isset($metadata->lifecycleCallbacks)) {
            $array['lifecycleCallbacks'] = $metadata->lifecycleCallbacks;
        }

        return \Symfony\Component\Yaml\Yaml::dump(array($metadata->name => $array), 10, $indent);
    }
}
