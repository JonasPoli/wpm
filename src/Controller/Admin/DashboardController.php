<?php

namespace App\Controller\Admin;

use App\Repository\BannerRepository;
use App\Repository\NewsRepository;
use App\Repository\ProductRepository;
use App\Repository\TestimonyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(): Response
    {

        return $this->render('admin/dashboard/index.html.twig', [

        ]);
    }
}
