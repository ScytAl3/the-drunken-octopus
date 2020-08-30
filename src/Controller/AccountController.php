<?php

namespace App\Controller;

use App\Entity\PurchaseOrder;
use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use App\Form\AccountIdentityFormType;
use App\Repository\PurchaseOrderRepository;
use App\Repository\PurchaseProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/account/{id<[0-9]+>}/identity/edit", name="app_account_identity_edit", methods={"GET", "PUT"})
     */
    public function identityEdit(Request $request, EntityManagerInterface $em, User $user, EmailVerifier $emailVerifier): Response
    {
        // Création du formulaire de modification de l'identité
        $form = $this->createForm(AccountIdentityFormType::class, $user, [
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);
        // Variable pour vérifier si l'utilisateur a ou non mofifier sont adresse email
        $checkNewEmail = false;

        if ($form->isSubmitted() && $form->isValid()) {
            // Permet d'obtenir l'unité de travail utilisée par l'EntityManager pour coordonner les opérations.
            $uow = $em->getUnitOfWork();
            // Calculer tous les changements qui ont été apportés aux entités et aux collections
            $uow->computeChangeSets();
            // Permet d'obtenir l'ensemble des changements pour une entité.
            $changeSet = $uow->getEntityChangeSet($user);
            // Si la requête post du formulaire contient le paramètre 'email'
            if (isset($changeSet['email'])) {
                $checkNewEmail = true;
            }
            $em->flush();

            // Si le champ email a été modifié
            if ($checkNewEmail) {
                // Génère une url signée et l'envoye par e-mail à l'utilisateur
                $emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    (new TemplatedEmail())
                        ->from(new Address('thedrunkenoctopus@iliveinabox.fr', 'The Drunken Octopus Mail'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );
                // Met à jour dans la table user le champ is_vérified à FALSE en attendant la confirmation
                $user->setIsVerified(false);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Your new email address needs to be verified.'
                );
            }

            $this->addFlash(
                'success',
                'User account identity information updated successfully!'
            );
            // Redirection sur la page principale du compte utilisateur
            return $this->redirectToRoute('app_account_index');
        }

        return $this->render('account/identity/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/address", name="app_account_address", methods={"GET"})
     */
    public function address()
    {
        return $this->render('account/addresses/address.html.twig', []);
    }

    /**
     * @Route("/account/{id<[0-9]+>}/order-history", name="app_account_order_history", methods={"GET"})
     */
    public function orderHistory(PurchaseOrderRepository $repo, User $user): Response
    {
        $orders = $repo->findOrderHistory($user->getId());
        // dd($orders);
        return $this->render('account/orders/order_history.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/account/order-history/{id<[0-9]+>}", name="app_account_order_show", methods={"GET"})
     */
    public function showOrder(PurchaseProductRepository $repo, PurchaseOrder $order): Response
    {
        $purchasedProducts = $repo->findPurchasedProducts($order->getId());
        // dd($purchasedProducts);
        return $this->render('account/orders/order_show.html.twig', [
            'purchasedProducts' => $purchasedProducts,
            'orderId' => $order->getId(),
            'date' => $order->getCreatedAt(),
        ]);
    }
}
