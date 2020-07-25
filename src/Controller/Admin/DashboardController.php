<?php

namespace App\Controller\Admin;

use App\Entity\Bottle;
use App\Entity\Brewery;
use App\Entity\Country;
use App\Entity\Product;
use App\Entity\Style;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN_PRODUCT")
 */
class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="app_admin_dashboard")
     */
    public function index(): Response
    {
        // redirect to some CRUD controller
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();

        return $this->redirect($routeBuilder->setController(ProductCrudController::class)->generateUrl());
        // return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('The Drunken Octopus');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Product', 'fa fa-beer', Product::class);
        yield MenuItem::linkToCrud('Style', 'fa fa-glass', Style::class);
        yield MenuItem::linkToCrud('Country', 'fa fa-flag', Country::class);
        yield MenuItem::linkToCrud('Brewery', 'fa fa-industry', Brewery::class);
        yield MenuItem::linkToCrud('Bottle', 'fa fa-flask', Bottle::class);
    }
}
