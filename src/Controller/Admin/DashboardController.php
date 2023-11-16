<?php

namespace App\Controller\Admin;

use App\Entity\Type;
use App\Entity\User;
use App\Entity\Model;
use App\Entity\Images;
use App\Entity\Comment;
use App\Entity\Contact;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Admin\ProductCrudController;
use App\Entity\Color;
use App\Entity\Critair;
use App\Entity\Energy;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ProductCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Application du garage Vincent Parrot');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Véhicules', 'fas fa-car', Product::class);
        yield MenuItem::linkToCrud('Catégorie', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Marques', 'fas fa-trademark', Model::class);
        yield MenuItem::linkToCrud('Modèles', 'fas fa-car-battery', Type::class);
        yield MenuItem::linkToCrud('Couleurs', 'fas fa-palette', Color::class);
        yield MenuItem::linkToCrud('Motorisation', 'fas fa-gas-pump', Energy::class);
        yield MenuItem::linkToCrud('Crit\'Air', 'fas fa-biohazard', Critair::class);
        if ($this->IsGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        }
        yield MenuItem::linkToCrud('Messages', 'fas fa-envelope', Contact::class);
        yield MenuItem::linkToCrud('Images', 'fas fa-image', Images::class);
        yield MenuItem::linkToCrud('Avis', 'fas fa-comment', Comment::class);
        yield MenuItem::linkToRoute('Retour au site', 'fas fa-home', 'homepage');
    }
}
