<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Style;
use App\Entity\Bottle;
use App\Entity\Brewery;
use App\Entity\Country;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

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
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('The Drunken Octopus')
            ->setFaviconPath('favicon/favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Dashboard', 'fa fa-table');

        // Section relative à la gestion des produits
        yield MenuItem::subMenu('Products', 'fa fa-beer')->setSubItems([
            MenuItem::linkToCrud('List Products', 'fa fa-th-list', Product::class)
                ->setDefaultSort(['title' => 'ASC']),
            MenuItem::linkToCrud('Style', 'fa fa-glass', Style::class)
                ->setDefaultSort(['label' => 'ASC']),
            MenuItem::linkToCrud('Country', 'fa fa-flag', Country::class)
                ->setDefaultSort(['label' => 'ASC']),
            MenuItem::linkToCrud('Brewery', 'fa fa-industry', Brewery::class)
                ->setDefaultSort(['label' => 'ASC']),
            MenuItem::linkToCrud('Bottle', 'fa fa-flask', Bottle::class)
                ->setDefaultSort(['capacity' => 'ASC'])
        ]);

        // Section relative aux utilisateurs
        yield MenuItem::linkToCrud('User', 'fa fa-users', User::class);

        // Section relative à la navigation sur le site
        yield MenuItem::section('Navigation', 'fa fa-folder-open');
        yield MenuItem::linktoRoute('Homepage', 'fa fa-home', 'app_home', []);
        yield MenuItem::linktoRoute('Products shop', 'fa fa-th', 'app_product_index', []);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user);
    }
}
