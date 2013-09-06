<?php

namespace Egzakt\SystemBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Egzakt\SystemBundle\Entity\Text;
use Egzakt\SystemBundle\Entity\TextRepository;
use Egzakt\SystemBundle\Lib\Frontend\BaseController;

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
        $this->textRepository = $this->getEm()->getRepository('EgzaktSystemBundle:Text');
    }

    /**
     * Index Action
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('EgzaktSystemBundle:Frontend/Text:index.html.twig');
    }

    /**
     * Display the main texts of a given section.
     * If no sectionId is provided the current section is used.
     *
     * @param int $sectionId
     *
     * @return Response
     */
    public function displayTextsAction($sectionId = null)
    {
        if (false == $sectionId) {
            $sectionId = $this->getSection()->getId();
        }

        $texts = $this->textRepository->findBy(array('section' => $sectionId, 'static' => false, 'active' => true), array('ordering' => 'ASC'));

        return $this->render('EgzaktSystemBundle:Frontend/Text:displayTexts.html.twig', array(
            'texts' => $texts,
            'textId' => $this->get('request')->get('bloc')
        ));
    }

    /**
     * Display a text by its id
     *
     * @param integer $textId
     *
     * @return Response
     */
    public function displayTextByIdAction($textId)
    {
        $text = $this->textRepository->findOneBy(array(
            'id' => $textId,
            'active' => true
        ));

        return $this->render('EgzaktSystemBundle:Frontend/Text:displayTexts.html.twig', array(
            'texts' => is_null($text) ? null : array($text),
            'textId' => $textId
        ));
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
        return $this->render('EgzaktSystemBundle:Frontend/Text:displayTexts.html.twig', array(
            'texts' => array($text),
            'textId' => $text->getId(),
        ));
    }
}
