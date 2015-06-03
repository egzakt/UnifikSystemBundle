<?php

namespace Unifik\SystemBundle\Controller\Frontend;

use Unifik\SystemBundle\Lib\Frontend\BaseController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MetadataController
 *
 * Render metadata tags based on a Metadatable Entity
 */
class MetadataController extends BaseController
{
    /**
     * Render Metadata tags
     *
     * @param string $metaName
     * @param bool   $ogMeta
     * @param bool   $forceEmpty
     *
     * @return Response
     */
    public function metadataAction($metaName, $ogMeta = false, $forceEmpty = false)
    {
        $metadatableGetter = $this->get('unifik_doctrine_behaviors.metadatable_getter');

        $element = $this->getCore()->getElement();

        $value = $metadatableGetter->getMetadata($element, $metaName);

        if (!$value && !$forceEmpty) {
            $parameter = sprintf('unifik_system.metadata.%s', $metaName);

            if ($this->container->hasParameter($parameter)) {
                $value = $this->container->getParameter($parameter);
            }
        }

        $view = $ogMeta ? 'UnifikSystemBundle:Frontend/Core:og_meta.html.twig' : 'UnifikSystemBundle:Frontend/Core:meta.html.twig';

        return $this->render($view, [
            'element' => $element,
            'meta_name' => $metaName,
            'value' => $value
        ]);
    }
}