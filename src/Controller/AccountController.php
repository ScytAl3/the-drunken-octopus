<?php

namespace App\Controller;

use App\Entity\PurchaseOrder;
use App\Entity\ShippingAddresses;
use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Address;
use App\Form\AccountIdentityFormType;
use App\Form\ShippingAddressFormType;
use App\Repository\PurchaseOrderRepository;
use App\Repository\PurchaseProductRepository;
use App\Repository\ShippingAddressesRepository;
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
     * @Route("/account/identity/edit", name="app_account_identity_edit", methods={"GET", "PUT"})
     */
    public function identityEdit(Request $request, EntityManagerInterface $em, EmailVerifier $emailVerifier): Response
    {
        // Recupère l'utilisateur courant
        $user = $this->getUser();
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
     * @Route("/account/address/list", name="app_account_address_list", methods={"GET"})
     */
    public function address_list(ShippingAddressesRepository $repo): Response
    {
        // Recupère l'utilisateur courant
        $user = $this->getUser();
        // Recupère les addresses associées
        $addresses = $repo->findBy(['user' => $user->getId()]);
        // dd($addresses);
        return $this->render('account/addresses/address_list.html.twig', [
            'addresses' => $addresses,
        ]);
    }

    /**
     * @Route("/account/address/create", name="app_account_address_create", methods={"GET", "POST"})
     */
    public function address_create(Request $request, EntityManagerInterface $em): Response
    {
        // Recupère l'utilisateur courant
        $user = $this->getUser();
        // Instanciation d'une nouvelle class ShippingAddresses
        $address = new ShippingAddresses;

        $form = $this->createForm(ShippingAddressFormType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($user);
            $em->persist($address);
            $em->flush();

            $this->addFlash(
                'success',
                'Address created successfully!'
            );

            // redirige vers la page qui montre les adresses associées au compte
            return $this->redirectToRoute('app_account_address_list', []);
        }
        
        return $this->render('account/addresses/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/address/{id<[0-9]+>}/edit", name="app_account_address_edit", methods={"GET", "PUT"})
     */
    public function address_edit(Request $request, EntityManagerInterface $em, ShippingAddresses $address): Response
    {
        // Recupère l'utilisateur courant
        $user = $this->getUser();
        // Création du formulaire de modification de l'adresse
        $form = $this->createForm(ShippingAddressFormType::class, $address, [
            'method' => 'PUT',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($address);
            $em->flush();

            $this->addFlash(
                'success',
                'Address updated successfully!'
            );

            // redirige vers la page qui montre les adresses associées au compte
            return $this->redirectToRoute('app_account_address_list', []);
        }

        return $this->render('account/addresses/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/address/{id<[0-9]+>}", name="app_account_address_delete", methods={"DELETE"})
     */
    public function address_delete(Request $request, EntityManagerInterface $em, ShippingAddresses $address): Response
    {
        if ($this->isCsrfTokenValid('address_delete_' . $address->getId(), $request->request->get('_csrf_token'))) {

            $em->remove($address);
            $em->flush();
        }

        $this->addFlash(
            'danger',
            'Shipping address deleted successfully!'
        );

        return $this->redirectToRoute('app_account_address_list');
    }
    /**
     * @Route("/account/order-history", name="app_account_order_history", methods={"GET"})
     */
    public function orderHistory(PurchaseOrderRepository $repo): Response
    {
        // Recupère l'utilisateur courant
        $user = $this->getUser();
        // Récupère l'historique des commandes
        $orders = $repo->findOrderHistory($user->getId());
        // dd($orders);
        return $this->render('account/orders/order_history.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/account/order-history/{id<[0-9]+>}/show", name="app_account_order_show", methods={"GET"})
     */
    public function showOrder(PurchaseProductRepository $repo, PurchaseOrder $order): Response
    {
        $purchasedProducts = $repo->findPurchasedProducts($order->getId());
        // dd($purchasedProducts);
        return $this->render('account/orders/order_show.html.twig', [
            'purchasedProducts' => $purchasedProducts,
            'orderId' => $order->getId(),
            'date' => $order->getCreatedAt(),
            'total' => $order->getTotalPrice(),
        ]);
    }
}
