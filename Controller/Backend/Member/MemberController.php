<?php

namespace Egzakt\SystemBundle\Controller\Backend\Member;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Egzakt\SystemBundle\Lib\Backend\BaseController;
use Egzakt\SystemBundle\Entity\Member;
use Egzakt\SystemBundle\Form\Backend\MemberType;

/**
 * Member Controller
 */
class MemberController extends BaseController
{
    /**
     * Init
     */
    public function init()
    {
        parent::init();

//        $this->getCore()->addNavigationElement($this->getSectionBundle());
    }

    /**
     * Lists all member entities.
     *
     * @return Response
     */
    public function listAction()
    {
        $members = $this->getEm()->getRepository('EgzaktSystemBundle:Member')->findAll();

        return $this->render('EgzaktSystemBundle:Backend/Member/Member:list.html.twig', array(
            'members' => $members
        ));
    }

    /**
     * Displays a form to edit an existing member entity or create a new one.
     *
     * @param integer $id The id of the Member to edit
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function editAction($id, Request $request)
    {
        /**
         * @var $member Member
         */
        $member = $this->getEm()->getRepository('EgzaktSystemBundle:Member')->find($id);

        if (false == $member) {
            $member = new Member();
            $member->setContainer($this->container);
        }

        $form = $this->createForm(new MemberType(), $member);

        if ('POST' == $request->getMethod()) {

            $previousEncodedPassword = $member->getPassword();

            $form->bindRequest($request);

            if ($form->isValid()) {

//                // New password set
//                if ($form->get('password')->getData()) {
//                    $encoder = $this->get('security.encoder_factory')->getEncoder($member);
//                    $encodedPassword = $encoder->encodePassword($member->getPassword(), $member->getSalt());
//                } else {
//                    $encodedPassword = $previousEncodedPassword;
//                }
//
//                $member->setPassword($encodedPassword);

                $this->getEm()->persist($member);
                $this->getEm()->flush();

                if ($request->request->has('save')) {
                    return $this->redirect($this->generateUrl('egzakt_system_backend_member'));
                }

                return $this->redirect($this->generateUrl($member->getRoute(), $member->getRouteParams()));
            }
        }

        return $this->render('EgzaktSystemBundle:Backend/Member/Member:edit.html.twig', array(
            'member' => $member,
            'form' => $form->createView()
        ));
    }

    /**
     * Deletes a Member entity.
     *
     * @param integer $id The id of the Member to delete
     *
     * @return Response|RedirectResponse
     * @throws NotFoundHttpException
     */
    public function deleteAction($id)
    {
        $entity = $this->getEm()->getRepository('EgzaktBackendMemberBundle:Member')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Member entity.');
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

        return $this->redirect($this->generateUrl($this->getBundleName()));
    }

}
