<?php

namespace Unifik\SystemBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Unifik\SystemBundle\Entity\Text;
use Unifik\SystemBundle\Entity\TextRepository;
use Unifik\SystemBundle\Lib\Frontend\BaseController;

/**
 * Text Controller
 */
class TextController extends BaseController
{
    /**
     * @var TextRepository
     */
    private $textRepository;

    /**
     * Init
     */
    public function init()
    {
        $this->textRepository = $this->getEm()->getRepository('UnifikSystemBundle:Text');
    }

    /**
     * Index Action
     *
     * @return Response
     */
    public function indexAction()
    {
        $response = new Response();
        $response->setPublic();
        $response->setSharedMaxAge(86400); // 1 day

        return $this->render(
            'UnifikSystemBundle:Frontend/Text:index.html.twig',
            [],
            $response
        );
    }

    /**
     * Display the main texts of a given section.
     * If no sectionId is provided the current section is used.
     *
     * @param Request $request
     * @param int $sectionId
     *
     * @return Response
     */
    public function displayTextsAction(Request $request, $sectionId = null)
    {
        if (false == $sectionId) {
            $sectionId = $this->getSection()->getId();
        }

        $textLastUpdate = $this->textRepository->findLastUpdate(null, $sectionId);

        $response = new Response();
        $response->setPublic();
        $response->setEtag($sectionId . $textLastUpdate);

        if ($response->isNotModified($request)) {
            return $response;
        }

        $texts = $this->textRepository->findBy(array('section' => $sectionId, 'static' => false, 'active' => true), array('ordering' => 'ASC'));

        return $this->render(
            'UnifikSystemBundle:Frontend/Text:displayTexts.html.twig',
            [
                'texts' => $texts,
                'textId' => $this->get('request')->get('bloc')
            ],
            $response
        );
    }

    /**
     * Display a text by its id
     *
     * @param Request $request
     * @param integer $textId
     *
     * @return Response
     */
    public function displayTextByIdAction(Request $request, $textId)
    {
        $textLastUpdate = $this->textRepository->findLastUpdate(null, null, $textId);

        $response = new Response();
        $response->setPublic();
        $response->setEtag($textId . $textLastUpdate);

        if ($response->isNotModified($request)) {
            return $response;
        }

        $text = $this->textRepository->findOneBy(array(
            'id' => $textId,
            'active' => true
        ));

        return $this->render(
            'UnifikSystemBundle:Frontend/Text:displayTexts.html.twig',
            [
                'texts' => is_null($text) ? null : array($text),
                'textId' => $textId
            ],
            $response
        );
    }

    /**
     * Display a single text
     *
     * @param Text $text
     *
     * @return Response
     */
    public function displayTextAction($text)
    {
        return $this->render('UnifikSystemBundle:Frontend/Text:displayTexts.html.twig', array(
            'texts' => array($text),
            'textId' => $text->getId(),
        ));
    }
}
