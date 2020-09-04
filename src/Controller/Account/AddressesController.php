<?php

namespace App\Controller\Account;

use App\Entity\ShippingAddresses;
use App\Form\ShippingAddressFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ShippingAddressesRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddressesController extends AbstractController
{
    /**
     * @Route("/account/addresses", name="app_account_address_list", methods={"GET"})
     */
    public function index(ShippingAddressesRepository $repo): Response
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
}
