<?php

namespace SymfonyDev\TCBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PaymentController extends Controller
{
    /**
     * @Template
     */
    public function indexAction(\Symfony\Component\HttpFoundation\Request $request)
    {
        $form = $this->createForm(
            \SymfonyDev\TCBundle\Form\PaymentInfoType::class,
            new \SymfonyDev\TCBundle\Entity\PaymentInfo(),
            array('action' => $this->generateUrl('tc_payment_update'))
        );

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Template
     */
    public function updateAction(\Symfony\Component\HttpFoundation\Request $request)
    {
        $form = $this->createForm(
            \SymfonyDev\TCBundle\Form\PaymentInfoType::class,
            new \SymfonyDev\TCBundle\Entity\PaymentInfo(),
            array('action' => $this->generateUrl('tc_payment_update'))
        );

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'ALERT_SUCCESS',
                'User information stored successfully.'
            );
            return $this->redirect($this->generateUrl('tc_payment_index'));
        }

        return array(
            'form' => $form->createView()
        );
    }
}
