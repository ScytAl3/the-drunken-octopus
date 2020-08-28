<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="app_account_index", methods={"GET"})
     */
    public function index()
    {
        return $this->render('account/index.html.twig', []);
    }

    /**
     * @Route("/account/identity", name="app_account_identity", methods={"GET"})
     */
    public function identity()
    {
        return $this->render('account/identity.html.twig', []);
    }

    /**
     * @Route("/account/address", name="app_account_address", methods={"GET"})
     */
    public function address()
    {
        return $this->render('account/address.html.twig', []);
    }

    /**
     * @Route("/account/order-history", name="app_account_order_history", methods={"GET"})
     */
    public function orderHistory()
    {
        return $this->render('account/order_history.html.twig', []);
    }
}
