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

        // Section relative à la gestion des produits
        yield MenuItem::section('Products management');
        yield MenuItem::linkToCrud('Product', 'fa fa-beer', Product::class);

        // Sous menu relatif à la gestion des "catégories"
        yield MenuItem::subMenu('Categories', 'fa fa-tags')->setSubItems([
            MenuItem::linkToCrud('Style', 'fa fa-glass', Style::class),
            MenuItem::linkToCrud('Country', 'fa fa-flag', Country::class),
            MenuItem::linkToCrud('Brewery', 'fa fa-industry', Brewery::class),
            MenuItem::linkToCrud('Bottle', 'fa fa-flask', Bottle::class)
        ]);
        
        // Section relative à la gestion des utilisateurs
        yield MenuItem::section('Users management');
        yield MenuItem::linkToCrud('User', 'fa fa-user', User::class);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user);
    }
}
