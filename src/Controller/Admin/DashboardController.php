<?php

namespace App\Controller\Admin;

use App\Entity\Offer;
use App\Entity\Reservation;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        // return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
        // Rediriger vers la page d'Offres par défaut
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(OfferCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration Voyage');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToRoute('Ajouter un Admin', 'fas fa-user-plus', 'app_register');
        yield MenuItem::linkToCrud('Administrateur', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Offres', 'fas fa-list', Offer::class);
        yield MenuItem::linkToCrud('Reservations', 'fas fa-list', Reservation::class);
    }
}
