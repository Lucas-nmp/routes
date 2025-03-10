<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Walk;
use App\Repository\WalkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Finder;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $walks = $entityManager->getRepository(Walk::class)->findAll();

        foreach ($walks as $walk) {
            $imageDir = 'images/uploads/' . $walk->getTitle() . '_' . $walk->getWalkDate()->format('d-m-y');
            $finder = new Finder();

            // Agregar imágenes a cada objeto walk
            if (is_dir($imageDir)) {
                $finder->files()->in($imageDir)->name('/\\.(jpg|jpeg|png)$/i');
                $images = [];

                foreach ($finder as $file) {
                    $images[] = $file->getRelativePathname();
                }

                $walk->images = $images;
            } else {
                $walk->images = [];
            }
        }


        return $this->render('home/index.html.twig', [
            'walks' => $walks,
        ]);
    }
}
