<?php

namespace App\Controller\Admin;

use App\Entity\PostText;
use App\Form\PostTextType;
use App\Form\PostTextTypeClean;
use App\Form\PostTextTypeDefout;
use App\Repository\PostTextRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/post-text')]
class PostTextController extends AbstractController
{
    #[Route('/', name: 'app_admin_post_text_index', methods: ['GET'])]
    public function index(PostTextRepository $postTextRepository): Response
    {
        return $this->render('admin/post_text/index.html.twig', [
            'post_texts' => $postTextRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_post_text_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $postText = new PostText();
        $form = $this->createForm(PostTextTypeClean::class, $postText);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($postText);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_post_text_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/post_text/new.html.twig', [
            'post_text' => $postText,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_post_text_show', methods: ['GET'])]
    public function show(PostText $postText): Response
    {
        return $this->render('admin/post_text/show.html.twig', [
            'post_text' => $postText,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_post_text_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PostText $postText, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PostTextTypeClean::class, $postText);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_post_text_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/post_text/edit.html.twig', [
            'post_text' => $postText,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_post_text_delete', methods: ['POST'])]
    public function delete(Request $request, PostText $postText, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$postText->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($postText);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_post_text_index', [], Response::HTTP_SEE_OTHER);
    }
}
