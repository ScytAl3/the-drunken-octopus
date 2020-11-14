<?php

namespace App\Service\Order;

// Include Dompdf required namespaces
use Dompdf\Dompdf;
use Dompdf\Options;

use App\Entity\User;
use Twig\Environment;
use App\Entity\PurchaseOrder;
use Symfony\Component\Mime\Address;
use App\Repository\PurchaseOrderRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Repository\PurchaseProductRepository;
use Symfony\Component\Mailer\MailerInterface;
use App\Repository\ShippingAddressesRepository;

/**
 * Service utilisé par OrdersHistoryController pour enregistrer une facture au format PDF et l'afficher dans un
 * nouvel onglet
 */
class OrderService
{
    /**
     * @var [type]
     */
    protected $purchaseOrderRepository;

    /**
     * @var [type]
     */
    protected $purchaseProductRepository;

    /**
     * @var [type]
     */
    protected $shippingAddressesRepository;

    /**
     * @var [type]
     */
    protected $twig;

    /**
     * @var string
     */
    protected $chrootDirectory;
    
    /**
     * @var [type]
     */
    protected $pdfDirectory;

    /**
     * @var [type]
     */
    protected $mailer;

    /**
     * Ajout des dépendances à la méthode __construct
     * @param PurchaseOrderRepository $purchaseOrderRepository 
     * @param PurchaseProductRepository $purchaseProductRepository 
     * @param ShippingAddressesRepository $shippingAddressesRepository 
     * @param Environment $twig 
     * @param string $chrootDirectory 
     * @param string $pdfDirectory 
     * @param MailerInterface $mailer 
     * @return void 
     */
    public function __construct(PurchaseOrderRepository $purchaseOrderRepository, PurchaseProductRepository $purchaseProductRepository, ShippingAddressesRepository $shippingAddressesRepository, Environment $twig, string $chrootDirectory, string $pdfDirectory, MailerInterface $mailer)
    {
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->purchaseProductRepository = $purchaseProductRepository;
        $this->shippingAddressesRepository = $shippingAddressesRepository;
        $this->twig = $twig;
        $this->chrootDirectory = $chrootDirectory;
        $this->pdfDirectory = $pdfDirectory;
        $this->mailer = $mailer;
    }

    /**
     * Création du template pour afficher la facture au format pdf
     *
     * @param PurchaseOrder $purchaseOrder
     * @return void
     */
    public function getOrderPDF(PurchaseOrder $purchaseOrder)
    {
        // Récupère la liste des produits associée à la commande
        $purchasedProducts = $this->purchaseProductRepository->findPurchasedProducts($purchaseOrder->getId());
        //
        // dd($purchaseOrder);
        //
        $shipping_address = $this->shippingAddressesRepository->find($purchaseOrder->getShippingAddress());

        // configure Dompdf according to the needs
        $pdfOptions = new Options;
        $pdfOptions->set('chroot', $this->chrootDirectory);
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsHtml5ParserEnabled(true);
        $pdfOptions->setDpi(150);

        // instantiate Dompdf with the options
        $dompdf = new Dompdf($pdfOptions);

        // retrieve the HTML generated in the twig file
        $html = $this->twig->render('pdf/invoice.html.twig', [
            'invoice_number' => '#FA-ODR_' . $purchaseOrder->getId() . '-USR_' . $purchaseOrder->getUser()->getId(),
            'purchaseOrder' => $purchaseOrder,
            'purchasedProducts' => $purchasedProducts,
            'total_ht' => $purchaseOrder->getTotalPrice(),
            'tva' => $purchaseOrder->getTotalPrice() * 0.2,
            'total_ttc' => $purchaseOrder->getTotalPrice() * 1.2,
            'shipping_address' => $shipping_address,
        ]);

        // load HTML to Dompdf
        $dompdf->loadHtml($html);
        //
        // dd($dompdf);
        //
        // (Optional) setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // render the HTML as PDF
        $dompdf->render();

        // store PDF Binary Data
        $output = $dompdf->output();

        //  write the file in the public directory set in config/services.yaml
        $publicDirectory = $this->pdfDirectory;
        // concatenate the name with the facture id
        $pdfFilepath =  $publicDirectory . '/order-' . $purchaseOrder->getId() . '-user-' . $purchaseOrder->getUser()->getId() . '.pdf';

        // write file to the desired path
        file_put_contents($pdfFilepath, $output);

        // output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }

    /**
     * Création du template et envoi de la facture par mail
     *
     * @param PurchaseOrder $purchaseOrder
     * @return void
     */
    public function mailOrderPDF(PurchaseOrder $purchaseOrder)
    {
        // Récupère la liste des produits associée à la commande
        $purchasedProducts = $this->purchaseProductRepository->findPurchasedProducts($purchaseOrder->getId());
        //
        // dd($purchaseOrder);
        //
        $shipping_address = $this->shippingAddressesRepository->find($purchaseOrder->getShippingAddress());

        // configure Dompdf according to the needs
        $pdfOptions = new Options;
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsHtml5ParserEnabled(true);
        $pdfOptions->setDpi(150);

        // instantiate Dompdf with the options
        $dompdf = new Dompdf($pdfOptions);

        // retrieve the HTML generated in the twig file
        $html = $this->twig->render('pdf/invoice.html.twig', [
            'invoice_number' => '#FA-ODR_' . $purchaseOrder->getId() . '-USR_' . $purchaseOrder->getUser()->getId(),
            'purchaseOrder' => $purchaseOrder,
            'purchasedProducts' => $purchasedProducts,
            'total_ht' => $purchaseOrder->getTotalPrice(),
            'tva' => $purchaseOrder->getTotalPrice() * 0.2,
            'total_ttc' => $purchaseOrder->getTotalPrice() * 1.2,
            'shipping_address' => $shipping_address,
        ]);

        // load HTML to Dompdf
        $dompdf->loadHtml($html);
        //
        // dd($dompdf);
        //
        // (Optional) setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // render the HTML as PDF
        $dompdf->render();

        // store PDF Binary Data
        $output = $dompdf->output();

        //  write the file in the public directory set in config/services.yaml
        $publicDirectory = $this->pdfDirectory;

        // concatenate the name with the facture id
        $pdfFilepath =  $publicDirectory . '/order-' . $purchaseOrder->getId() . '-user-' . $purchaseOrder->getUser()->getId() . '.pdf';
        // write file to the desired path
        file_put_contents($pdfFilepath, $output);

        // Envoie de la facture par mail
        $email = (new TemplatedEmail())
            ->from(new Address('thedrunkenoctopus@iliveinabox.fr', 'The Drunken Octopus Mail'))
            ->to($purchaseOrder->getUser()->getEmail())
            ->subject('Your invoice in PDF format')
            ->htmlTemplate('cart/invoice_email.html.twig')
            ->attachFromPath($pdfFilepath)
        ;

        $this->mailer->send($email);        
    }
}
