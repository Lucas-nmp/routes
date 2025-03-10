<?php

namespace App\Controller;

use App\Entity\Walk;
use App\Form\WalkType;
use App\Repository\WalkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/walk')]
final class WalkController extends AbstractController
{
    #[Route(name: 'app_walk_index', methods: ['GET'])]
    public function index(WalkRepository $walkRepository): Response
    {

        $walks = $walkRepository->findAll();

        $walksArray = array_map(function($walk) {
            return [
                'id' => $walk->getId(),
                'name' => $walk->getTitle(),
                'dateWalk' => $walk->getWalkDate() ? $walk->getWalkDate()->format('Y-m-d') : '',
                'description' => $walk->getDescription(),
                'csrf_token' => $this->container->get('security.csrf.token_manager')->getToken('delete' . $walk->getId())->getValue(),
            ];
        }, $walks);

        return $this->render('walk/index.html.twig', [
            'walks' => $walks,
            'walksJson' => json_encode($walksArray),
        ]);
    }

    #[Route('/new', name: 'app_walk_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $walk = new Walk();
        $form = $this->createForm(WalkType::class, $walk);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($walk);
            $entityManager->flush();

            // Subir imágenes
            $images = $request->files->get('images');
            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/images/uploads/' . $walk->getTitle() . '_' . $walk->getWalkDate()->format('d-m-y');

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); 
            }
            
            if ($images) {
                foreach ($images as $image) {
                    if ($image->isValid() && in_array($image->getMimeType(), ['image/jpeg', 'image/png', 'image/gif'])) {
                        try {
                            $newFilename = uniqid() . '.' . $image->guessExtension();
                            $image->move($uploadDir, $newFilename);
                        } catch (FileException $e) {
                            $this->addFlash('error', 'No se pudo subir la imagen: ' . $e->getMessage());
                        }
                    } else {
                        $this->addFlash('error', 'Archivo no válido: ' . $image->getClientOriginalName());
                    }
                }
            } else {
                $this->addFlash('error', 'No se seleccionaron imágenes.');
            }

            return $this->redirectToRoute('app_walk_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('walk/new.html.twig', [
            'walk' => $walk,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_walk_show', methods: ['GET'])]
    public function show(Walk $walk): Response
    {
        return $this->render('walk/show.html.twig', [
            'walk' => $walk,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_walk_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Walk $walk, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WalkType::class, $walk);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_walk_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('walk/edit.html.twig', [
            'walk' => $walk,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_walk_delete', methods: ['POST'])]
    public function delete(Request $request, Walk $walk, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$walk->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($walk);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_walk_index', [], Response::HTTP_SEE_OTHER);
    }
}
