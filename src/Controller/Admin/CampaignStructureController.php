<?php

namespace App\Controller\Admin;

use App\Entity\CampaignStructure;
use App\Form\CampaignStructureType;
use App\Repository\CampaignStructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/campaign-structure')]
class CampaignStructureController extends AbstractController
{
    #[Route('/', name: 'app_admin_campaign_structure_index', methods: ['GET'])]
    public function index(CampaignStructureRepository $campaignStructureRepository): Response
    {
        return $this->render('admin/campaign_structure/index.html.twig', [
            'campaign_structures' => $campaignStructureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_campaign_structure_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $campaignStructure = new CampaignStructure();
        $form = $this->createForm(CampaignStructureType::class, $campaignStructure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($campaignStructure);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_campaign_structure_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/campaign_structure/new.html.twig', [
            'campaign_structure' => $campaignStructure,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_campaign_structure_show', methods: ['GET'])]
    public function show(CampaignStructure $campaignStructure): Response
    {
        return $this->render('admin/campaign_structure/show.html.twig', [
            'campaign_structure' => $campaignStructure,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_campaign_structure_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CampaignStructure $campaignStructure, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CampaignStructureType::class, $campaignStructure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_campaign_structure_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/campaign_structure/edit.html.twig', [
            'campaign_structure' => $campaignStructure,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_campaign_structure_delete', methods: ['POST'])]
    public function delete(Request $request, CampaignStructure $campaignStructure, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campaignStructure->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($campaignStructure);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_campaign_structure_index', [], Response::HTTP_SEE_OTHER);
    }
}
