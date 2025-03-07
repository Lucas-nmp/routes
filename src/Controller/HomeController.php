<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Walk;
use App\Repository\WalkRepository;
use Doctrine\ORM\EntityManagerInterface;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $walks = $entityManager->getRepository(Walk::class)->findAll();

        return $this->render('home/index.html.twig', [
            'walks' => $walks,
        ]);
    }
}
