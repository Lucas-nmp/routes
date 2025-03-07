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
        return $this->render('walk/index.html.twig', [
            'walks' => $walkRepository->findAll(),
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
