<?php

namespace App\Controller\Admin;

use App\Entity\Campaign;
use App\Entity\Client;
use App\Entity\Post;
use App\Entity\PostText;
use App\Entity\CampaignStructure;
use App\Enum\MidiaEnum;
use App\Form\PostStep1Type;
use App\Form\PostStep2Type;
use App\Form\PostStep3Type;
use App\Form\PostStep4Type;
use App\Form\PostTextType;
use App\Form\PostType;
use App\Repository\ClientRepository;
use App\Repository\CampaignRepository;
use App\Repository\PostRepository;
use App\Repository\CampaignStructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostStepController extends AbstractController
{
    #[Route('/post-step/new/{step}/', name: 'post_step_new')]
    public function new(
        Request $request,
        ClientRepository $clientRepository,
        CampaignRepository $campaignRepository,
        CampaignStructureRepository $campaignStructureRepository,
        EntityManagerInterface $em
    ): Response {
        $step = $request->get('step', 1);

        switch ($step) {
            case 1:
                return $this->handleStep1($request, $clientRepository);
            case 2:
                return $this->handleStep2($request, $campaignRepository);
            case 3:
                return $this->handleStep3($request, $campaignRepository, $em);
            case 4:
                return $this->handleStep4($request, $em);
            case 5:
                return $this->handleStep5($request, $campaignStructureRepository, $em);
            case 6:
                return $this->handleStep6($request, $em);
            default:
                return $this->redirectToRoute('post_step_new', ['step' => 1]);
        }
    }

    private function handleStep1(Request $request, ClientRepository $clientRepository): Response
    {
        $clients = $clientRepository->findAll();

        return $this->render('admin/postStep/step1.html.twig', [
            'clients' => $clients,
        ]);
    }

    private function handleStep2(Request $request, CampaignRepository $campaignRepository): Response
    {
        $clientId = $request->get('client_id');
        $campaigns = $campaignRepository->findBy(['client' => $clientId]);

        return $this->render('admin/postStep/step2.html.twig', [
            'campaigns' => $campaigns,
        ]);
    }

    private function handleStep3(Request $request, CampaignRepository $campaignRepository, EntityManagerInterface $entityManager): Response
    {
        $post = new Post();
        $post->setCreatedBy($this->getUser());
        $form = $this->createForm(PostStep3Type::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_step_new', ['step' => 5, 'post_id' => $post->getId()]);
        }


        $campaignId = $request->get('campaign_id');
        $campaign = $campaignRepository->find($campaignId);

        return $this->render('admin/postStep/step3.html.twig', [
            'post' => $post,
            'form' => $form,
            'campaign' => $campaign,
        ]);

    }

    private function handleStep4(Request $request, EntityManagerInterface $em): Response
    {
        dd($campaign = $request->request->all());
        $post = new Post();
        $post->setCreatedBy($this->getUser());
        $form = $this->createForm(PostStep3Type::class, $post);
        $form->handleRequest($request);

        $em->persist($post);
        $em->flush();

        return $this->redirectToRoute('post_step_new', ['step' => 5, 'post_id' => $post->getId()]);
    }

    private function handleStep5(Request $request,  CampaignStructureRepository $campaignStructureRepository, EntityManagerInterface $em): Response
    {

        $postId = $request->get('post_id');
        $post = $em->getRepository(Post::class)->find($postId);

        $campaignId = $post->getCampaign()->getId();
        $campaignStructures = $campaignStructureRepository->findBy(['campaign' => $campaignId]);

        $fields = [];
        foreach ($campaignStructures as $campaignStructure) {
            $fields[] = ['id' => $campaignStructure->getId(),'title' => $campaignStructure->getTitle()];
        }


        $submited = $request->request->get('submited');
        $allValues = $request->request->all();
        if ($submited){
            // salvar os textos
            foreach ($allValues['fields'] as $index => $field) {
                $campaignStructure = $campaignStructureRepository->find($index);
                $newText = new PostText();
                $newText->setPost($post);
                $newText->setCampaingStructure($campaignStructure);
                $newText->setContent($field);
                $em->persist($newText);
                $em->flush();
            }
            return $this->redirectToRoute('post_step_new', ['step' => 6, 'post_id' => $post->getId()]);
        }


        return $this->render('admin/postStep/step5.html.twig', [
            'post' => $post,
            'campaignStructures' => $campaignStructures,
            'fields' => $fields,
        ]);
    }


    private function handleStep6(Request $request, EntityManagerInterface $em): Response
    {
        $postId = $request->get('post_id');
        $post = $em->getRepository(Post::class)->find($postId);

        return $this->render('admin/postStep/step6.html.twig', [
            'post' => $post,
        ]);
    }
}
