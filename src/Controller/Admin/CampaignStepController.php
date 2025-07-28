<?php

namespace App\Controller\Admin;

use App\Entity\Campaign;
use App\Entity\CampaignStructure;
use App\Entity\Post;
use App\Entity\PostText;
use App\Form\CampaignPeriodType;
use App\Form\CampaignStep2StructureType;
use App\Form\CampaignStep2Type;
use App\Form\CampaignStructureType;
use App\Form\CampaignType;
use App\Form\PostStep5Type;
use App\Form\PostTextTypeClean;
use App\Form\PostType;
use App\Repository\CampaignRepository;
use App\Repository\CampaignStructureRepository;
use App\Repository\ClientRepository;
use App\Repository\CommemorativeDatesRepository;
use App\Repository\PostRepository;
use App\Service\BannerItemService;
use App\Service\PostService;
use App\Service\UnsplashService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/campaign-step')]
class CampaignStepController extends AbstractController
{
    private $unsplashApiKey;
    #[Route('/', name: 'app_admin_campaign_step1', methods: ['GET'])]
    public function step1(ClientRepository $clientRepository): Response
    {
        return $this->render('admin/campaign_step/step1.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
    }

    #[Route('/2', name: 'app_admin_campaign_step2', methods: ['GET', 'POST'])]
    public function step2(Request $request, EntityManagerInterface $entityManager, ClientRepository $clientRepository): Response
    {
        $campaign = new Campaign();
        $form = $this->createForm(CampaignStep2Type::class, $campaign);
        $form->handleRequest($request);
        $client = $clientRepository->find($request->get('client_id'));

        if ($form->isSubmitted() && $form->isValid()) {
            $campaign->setClient($client);
            $entityManager->persist($campaign);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_campaign_step3', ['campaignId' => $campaign->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/campaign_step/step2.html.twig', [
            'campaign' => $campaign,
            'client' => $client,
            'form' => $form,
        ]);
    }


    #[Route('/3/{campaignId}', name: 'app_admin_campaign_step3', methods: ['GET', 'POST'])]
    public function step3($campaignId, CampaignRepository $campaignRepository): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        return $this->render('admin/campaign_step/step3_index.html.twig', [
            'campaign' => $campaign,
        ]);
    }

    #[Route('/3/{campaignId}/new', name: 'app_admin_campaign_step3_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,$campaignId, CampaignRepository $campaignRepository): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        $campaignStructure = new CampaignStructure();
        $form = $this->createForm(CampaignStep2StructureType::class, $campaignStructure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campaignStructure->setCampaign($campaign);
            $entityManager->persist($campaignStructure);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_campaign_step3', ['campaignId' => $campaign->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/campaign_step/step3_new.html.twig', [
            'campaign' => $campaign,
            'campaign_structure' => $campaignStructure,
            'form' => $form,
        ]);
    }

    #[Route('/3/{campaignId}/{id}/edit', name: 'app_admin_campaign_step3_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CampaignStructure $campaignStructure, EntityManagerInterface $entityManager,$campaignId, CampaignRepository $campaignRepository): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        $form = $this->createForm(CampaignStep2StructureType::class, $campaignStructure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $campaignStructure->setCampaign($campaign);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_campaign_step3_edit', ['campaignId' => $campaign->getId(),'id' => $campaignStructure->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/campaign_step/step3_edit.html.twig', [
            'campaign' => $campaign,
            'campaign_structure' => $campaignStructure,
            'form' => $form,
        ]);
    }

    #[Route('/3/{campaignId}/{id}', name: 'app_admin_campaign_step3_delete', methods: ['POST'])]
    public function delete(Request $request, CampaignStructure $campaignStructure, EntityManagerInterface $entityManager,$campaignId, CampaignRepository $campaignRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campaignStructure->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($campaignStructure);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_campaign_step3', ['campaignId' => $campaignId], Response::HTTP_SEE_OTHER);
    }

    #[Route('/4/{campaignId}', name: 'app_admin_campaign_step4', methods: ['GET','POST'])]
    public function step4(Request $request, ClientRepository $clientRepository,$campaignId, CampaignRepository $campaignRepository): Response
    {

        $campaign = $campaignRepository->find($campaignId);
        $form = $this->createForm(CampaignPeriodType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $periodicidade = $form->get('periodicidade')->getData();
            $startDate = $campaign->getStardDate(); // Data inicial
            $endDate = $campaign->getEndDate(); // Data final

            $postDates = $this->planPostings($periodicidade, $startDate, $endDate);
            setlocale(LC_TIME, 'pt_BR.UTF-8'); // Define a localidade para português do Brasil

            return $this->render('admin/campaign_step/step4_list.html.twig', [
                'postDates' => $postDates,
                'campaign' => $campaign,
            ]);
        }

        return $this->render('admin/campaign_step/step4_period.html.twig', [
            'form' => $form->createView(),
            'campaign' => $campaign,
        ]);


    }


    private function planPostings(string $periodicidade,  $startDate,  $endDate): array
    {
        $dates = [];
        $intervalMap = [
            '2_weekly_tue_thu' => ['Tuesday', 'Thursday'],
            '3_weekly_mon_wed_fri' => ['Monday', 'Wednesday', 'Friday'],
            '4_weekly_mon_tue_thu_fri' => ['Monday', 'Tuesday', 'Thursday', 'Friday'],
            '1_weekly_wed' => ['Wednesday'],
            '5_weekly_mon_fri' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            '2_weekly_mon_fri' => ['Monday', 'Friday'],
            '3_weekly_tue_thu_sat' => ['Tuesday', 'Thursday', 'Saturday'],
            '6_weekly_mon_sat' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            '7_weekly_mon_sun' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            '4_weekly_sun_tue_thu_sat' => ['Sunday', 'Tuesday', 'Thursday', 'Saturday'],
        ];

        if (!array_key_exists($periodicidade, $intervalMap)) {
            throw new \InvalidArgumentException("Periodicidade inválida.");
        }

        $daysOfWeek = $intervalMap[$periodicidade];
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($startDate, $interval, $endDate->add($interval));

        foreach ($period as $date) {
            if (in_array($date->format('l'), $daysOfWeek)) {
                $dates[] = $date;
            }
        }

        return $dates;
    }

    #[Route('/5/{campaignId}/create-posts', name: 'app_admin_campaign_step5_create', methods: ['GET','POST'])]
    public function step5(Request $request, ClientRepository $clientRepository, $campaignId, CampaignRepository $campaignRepository, EntityManagerInterface $entityManager): Response
    {
        $campaign = $campaignRepository->find($campaignId);

        // Obtém a string JSON do campo postDates
        $postDatesJson = $request->request->get('postDates');

        // Decodifica a string JSON para um array
        $postDatesArray = json_decode($postDatesJson, true);


        foreach ($postDatesArray as $index => $date) {
            $newPost = new Post();
            $newPost->setCreatedBy($this->getUser());
            $newPost->setCampaign($campaign);

            // separar data e hora
            $dateSepare = explode(" ", $date['date']);
            // Cria um objeto DateTime a partir da string da data
            $newdate = new \DateTime($dateSepare[0].$campaign->getPostingTime()->format(' H:i '). ' +03:00');
            $newPost->setScheduleDate($newdate);
            $entityManager->persist($newPost);
            $entityManager->flush();

            foreach ($campaign->getCampaignStructures() as $index => $campaignStructure) {
                // Criando os textos
                $newText = new PostText();
                $newText->setContent($campaignStructure->getTitle());
                $newText->setPost($newPost);
                $newText->setCampaingStructure($campaignStructure);
                $entityManager->persist($newText);
                $entityManager->flush();
            }

        }
        return $this->redirectToRoute('app_admin_campaign_step5', ['campaignId' => $campaign->getId()], Response::HTTP_SEE_OTHER);

    }

    #[Route('/5/{campaignId}', name: 'app_admin_campaign_step5', methods: ['GET', 'POST'])]
    public function step5List($campaignId, CampaignRepository $campaignRepository, UnsplashService $unsplashService, string $unsplashApiKey): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        //$photos = $unsplashService->searchImages('Site seguro',2);
        $photos = [];
        //dd($photos);
        return $this->render('admin/campaign_step/step5_index.html.twig', [
            'campaign' => $campaign,
            'photos' => $photos,
        ]);
    }


    #[Route('/5/{campaignId}/new', name: 'app_admin_campaign_step5_post_new', methods: ['GET', 'POST'])]
    public function step5new(Request $request, EntityManagerInterface $entityManager, $campaignId, CampaignRepository $campaignRepository): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        $post = new Post();
        $post->setCreatedBy($this->getUser());
        $form = $this->createForm(PostStep5Type::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCampaign($campaign);
            $entityManager->persist($post);
            $entityManager->flush();
            foreach ($campaign->getCampaignStructures() as $index => $campaignStructure) {
                // Criando os textos
                $newText = new PostText();
                $newText->setContent($campaignStructure->getTitle());
                $newText->setPost($post);
                $newText->setCampaingStructure($campaignStructure);
                $entityManager->persist($newText);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_admin_campaign_step5', ['campaignId' => $campaign->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/campaign_step/step5_new.html.twig', [
            'campaign' => $campaign,
            'post' => $post,
            'form' => $form,
        ]);
    }



    #[Route('/5/{campaignId}/{id}/edit', name: 'app_admin_campaign_step5_post_edit', methods: ['GET', 'POST'])]
    public function step5edit(Request $request, Post $post, EntityManagerInterface $entityManager, $campaignId, CampaignRepository $campaignRepository, BannerItemService $bannerItemService): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        $form = $this->createForm(PostStep5Type::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCampaign($campaign);
            $entityManager->flush();

            // Verificar se a imagem foi alterada
            $submittedImage = $form->get('imageFile')->getData();
            if ($submittedImage !== null ) {
                // A imagem foi alterada
                $post->setDinamicImage($bannerItemService->getImageDataFromPost($post));
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_admin_campaign_step5', ['campaignId' => $campaign->getId()], Response::HTTP_SEE_OTHER);
        }


        // Verificar se nao tem a arte

        if ($post->getDinamicImage() == null  && $post->getImage() !== null) {
            // A imagem foi alterada
            $post->setDinamicImage($bannerItemService->getImageDataFromPost($post));
            $entityManager->flush();
        }

        return $this->render('admin/campaign_step/step5_edit.html.twig', [
            'campaign' => $campaign,
            'post' => $post,
            'form' => $form,
        ]);
    }
    #[Route('/5/{campaignId}/{id}/aprove', name: 'app_admin_campaign_step5_post_aprove', methods: ['GET', 'POST'])]
    public function step5aprove(Request $request, Post $post, EntityManagerInterface $entityManager, $campaignId, CampaignRepository $campaignRepository): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        $post->setApprovedBy($this->getUser());
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_campaign_step5_post_edit', ['campaignId' => $campaign->getId(),'id' => $post->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/5/{campaignId}/{id}/reprove', name: 'app_admin_campaign_step5_post_reprove', methods: ['GET', 'POST'])]
    public function step5reprove(Request $request, Post $post, EntityManagerInterface $entityManager, $campaignId, CampaignRepository $campaignRepository): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        $post->setApprovedBy(null);
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_campaign_step5_post_edit', ['campaignId' => $campaign->getId(),'id' => $post->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/5/{campaignId}/{id}/schedulePost', name: 'app_admin_campaign_step5_post_schedule', methods: ['GET', 'POST'])]
    public function step5schedulePost(Request $request, Post $post, EntityManagerInterface $entityManager, $campaignId, CampaignRepository $campaignRepository, PostService $postService, Session $session): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        if ($post->getImage() == ''){
            $session = new Session();

            $flashes = $session->getFlashBag();
            // add flash messages
            $flashes->add('error', 'O Post não tem imagem');
        }
        $data = $postService->schedulePost($post);
        //dump($data);
        //exit;
        return $this->redirectToRoute('app_admin_campaign_step5', ['campaignId' => $campaign->getId()], Response::HTTP_SEE_OTHER);
    }


    #[Route('/5/{campaignId}/{id}/removeSchedulePost', name: 'app_admin_campaign_step5_remove_post_schedule', methods: ['GET', 'POST'])]
    public function step5removeSchedulePost(Request $request, Post $post, EntityManagerInterface $entityManager, $campaignId, CampaignRepository $campaignRepository, PostService $postService): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        $data = $postService->removeSchedulePost($post);
        //dump($data);
        //exit;
        return $this->redirectToRoute('app_admin_campaign_step5_post_edit', ['campaignId' => $campaign->getId(),'id' => $post->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/5/{campaignId}/{id}', name: 'app_admin_campaign_step5_post_delete', methods: ['POST'])]
    public function step5delete(Request $request, Post $post, EntityManagerInterface $entityManager, $campaignId, CampaignRepository $campaignRepository): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            foreach ($post->getPostTexts() as $index => $postText) {
                $entityManager->remove($postText);
                $entityManager->flush();
            }
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_campaign_step5', ['campaignId' => $campaign->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/5/{campaignId}/post-text/{id}/edit', name: 'app_admin_campaign_step5_post_text_edit', methods: ['GET', 'POST'])]
    public function step5TextEdit(Request $request, PostText $postText, EntityManagerInterface $entityManager, $campaignId, CampaignRepository $campaignRepository, CommemorativeDatesRepository $commemorativeDatesRepository): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        $form = $this->createForm(PostTextTypeClean::class, $postText);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_campaign_step5', ['campaignId' => $campaign->getId()], Response::HTTP_SEE_OTHER);
        }

        $commemorativeDates = $commemorativeDatesRepository->findCommemorativeDatesAroundToday($postText->getPost()->getScheduleDate());
        //dd($commemorativeDates);

        return $this->render('admin/campaign_step/step5_text_form.html.twig', [
            'campaign' => $campaign,
            'post_text' => $postText,
            'form' => $form,
            'commemorativeDates' => $commemorativeDates,
        ]);
    }



}