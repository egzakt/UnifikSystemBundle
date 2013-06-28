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

                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans(
                    '%entity% has been updated.',
                    array('%entity%' => $member))
                );

                if ($request->request->has('save')) {
                    return $this->redirect($this->generateUrl('egzakt_system_backend_member'));
                }

                return $this->redirect($this->generateUrl($member->getRoute(), $member->getRouteParams()));
            } else {
                $this->get('session')->getFlashBag()->add('error', 'Some fields are invalid.');
            }
        }

        return $this->render('EgzaktSystemBundle:Backend/Member/Member:edit.html.twig', array(
            'member' => $member,
            'form' => $form->createView()
        ));
    }

    /**
     * Delete a Member entity.
     *
     * @param Request $request
     * @param int $id
     *
     * @return RedirectResponse|Response
     *
     * @throws NotFoundHttpException
     */
    public function deleteAction(Request $request, $id)
    {
        $member = $this->getEm()->getRepository('EgzaktSystemBundle:Member')->find($id);

        if (!$member) {
            throw $this->createNotFoundException('Unable to find a member entity using id "' . $id . '".');
        }

        if ($request->get('message')) {
            $template = $this->renderView('EgzaktSystemBundle:Backend/Core:delete_message.html.twig', array(
                'entity' => $member
            ));

            return new Response(json_encode(array(
                'template' => $template,
                'isDeletable' => $member->isDeletable()
            )));
        }

        // Call the translator before we flush the entity so we can have the real __toString()
        $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans(
            '%entity% has been deleted.',
            array('%entity%' => $member != '' ? $member : $member->getEntityName()))
        );

        $this->getEm()->remove($member);
        $this->getEm()->flush();

        return $this->redirect($this->generateUrl('egzakt_system_backend_member'));
    }

}
