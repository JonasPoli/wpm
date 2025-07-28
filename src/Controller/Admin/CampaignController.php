<?php

namespace App\Controller\Admin;

use App\Entity\Campaign;
use App\Entity\Post;
use App\Entity\PostText;
use App\Form\CampaignType;
use App\Repository\CampaignRepository;
use App\Repository\CampaignStructureRepository;
use App\Repository\CommemorativeDatesRepository;
use App\Repository\PostRepository;
use App\Service\CsvService;
use App\Service\PostService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/campaign')]
class CampaignController extends AbstractController
{
    #[Route('/', name: 'app_admin_campaign_index', methods: ['GET'])]
    public function index(CampaignRepository $campaignRepository): Response
    {
        return $this->render('admin/campaign/index.html.twig', [
            'campaigns' => $campaignRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_campaign_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $campaign = new Campaign();
        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($campaign);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_campaign_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/campaign/new.html.twig', [
            'campaign' => $campaign,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_campaign_show', methods: ['GET'])]
    public function show(Campaign $campaign): Response
    {
        return $this->render('admin/campaign/show.html.twig', [
            'campaign' => $campaign,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_campaign_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Campaign $campaign, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_campaign_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/campaign/edit.html.twig', [
            'campaign' => $campaign,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_campaign_delete', methods: ['POST'])]
    public function delete(Request $request, Campaign $campaign, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campaign->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($campaign);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_campaign_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{campaignId}/structure', name: 'app_admin_campaign_structure_index_full', methods: ['GET'])]
    public function indexStructure($campaignId, CampaignStructureRepository $campaignStructureRepository, CampaignRepository $campaignRepository): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        return $this->render('admin/campaign_structure/index.html.twig', [
            'campaign_structures' => $campaignStructureRepository->findBy(['campaign'=>$campaign]),
            'campaign' => $campaign,
        ]);
    }
    #[Route('/{campaignId}/post', name: 'app_admin_post_index_full', methods: ['GET'])]
    public function indexPost($campaignId, PostRepository $postRepository, CampaignRepository $campaignRepository): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        return $this->render('admin/post/index.html.twig', [
            'posts' => $postRepository->findBy(['campaign'=>$campaign]),
            'campaign' => $campaign,
        ]);
    }

    #[Route('/{campaignId}/FacebookSchedulePost/', name: 'app_admin_campaign_get_facebook_schedule_post', methods: ['GET', 'POST'])]
    public function getFacebookSchedulePost(Request $request,  EntityManagerInterface $entityManager, $campaignId, CampaignRepository $campaignRepository, PostService $postService): Response
    {
        $campaign = $campaignRepository->find($campaignId);
        $data = $postService->getAllSchedulePost($campaign);
        dump($data);
        exit;
    }


    #[Route('/{campaignId}/generatePromptStep1/', name: 'app_admin_campaign_generate_prompt_step1', methods: ['GET', 'POST'])]
    public function getPromptStep1(Request $request, $campaignId, CampaignRepository $campaignRepository, CommemorativeDatesRepository $commemorativeDatesRepository, PostRepository $postRepository): Response
    {
        $campaign = $campaignRepository->find($campaignId);

        $queryBuilder = $postRepository->createQueryBuilder('p');
        $queryBuilder->where('p.campaign = :campaign')
            ->andWhere('p.scheduleDate IS NOT NULL')
            ->setParameter('campaign', $campaign)
            ->orderBy('p.scheduleDate', 'ASC');

        $allPosts = $queryBuilder->getQuery()->getResult();

        // Determinar primeiro post
        //$allPosts = $postRepository->findBy(['campaign'=>$campaign],['scheduleDate'=>'ASC']);
        $fisrtDate =  array_shift($allPosts);
        $lastDate =  array_pop($allPosts);
        //dd($fisrtDate->getScheduleDate(), $lastDate->getScheduleDate());
        $commemorativeDates = $commemorativeDatesRepository->findCommemorativeDatesAroundInterval($fisrtDate->getScheduleDate(), $lastDate->getScheduleDate());
        $campaign = $campaignRepository->find($campaignId);


        return $this->render('admin/campaign/dates_index.html.twig', [
            'posts' => $postRepository->findBy(['campaign'=>$campaign]),
            'campaign' => $campaign,
            'commemorativeDates' => $commemorativeDates,
        ]);



    }

    #[Route('/{campaignId}/generatePromptStep2/', name: 'app_admin_campaign_generate_prompt_step2', methods: ['GET', 'POST'])]
    public function getPromptStep2(Request $request, $campaignId, CampaignRepository $campaignRepository, CommemorativeDatesRepository $commemorativeDatesRepository, PostRepository $postRepository, PostService $postService): Response
    {
        $campaign = $campaignRepository->find($campaignId);

        $commemorativeDates = $request->query->all();
        $choices = [];
        if (isset($commemorativeDates['form'])){
            $choices = $commemorativeDates['form']['commemorativeDates'];
        }


        $queryBuilder = $commemorativeDatesRepository->createQueryBuilder('c');
        $queryBuilder
            ->andWhere('c.id IN (:choices)')
            ->setParameter('choices', $choices)
            ->orderBy('c.month', 'ASC');

        $allPosts = $queryBuilder->getQuery()->getResult();

        $history = $postService->getHistoryPost($campaign->getClient());

        $dataHistory = [];
        if ($history['success'] == true){
            $dataHistory = $history['data'];
        }

        return $this->render('admin/campaign/prompt.html.twig', [
            'campaign' => $campaign,
            'allPosts' => $allPosts,
            'history' => $dataHistory,
        ]);
    }

    #[Route('/{campaignId}/generatePromptStep3/', name: 'app_admin_campaign_generate_prompt_step3', methods: ['GET', 'POST'])]
    public function getPromptStep3(Request $request, $campaignId, CampaignRepository $campaignRepository, CommemorativeDatesRepository $commemorativeDatesRepository, PostRepository $postRepository, PostService $postService): Response
    {
        $campaign = $campaignRepository->find($campaignId);

        return $this->render('admin/campaign/upload_csv.html.twig', [
            'campaign' => $campaign,
        ]);
    }

    #[Route('/{campaignId}/generatePromptStep4', name: 'app_admin_campaign_generate_prompt_step4', methods: ['POST'])]
    public function getPromptStep4(
        Request $request,
        int $campaignId,
        CampaignRepository $campaignRepository,
        CommemorativeDatesRepository $commemorativeDatesRepository,
        PostRepository $postRepository,
        CsvService $csvService,
    ): Response {
        $campaign = $campaignRepository->find($campaignId);

        /** @var UploadedFile $csvFile */
        $csvFile = $request->files->get('csv_file');

        $csvRows='';
        if ($csvFile && $csvFile->isValid()) {
            try {
                $csvRows = $csvService->readFile($csvFile->getPathname());
                // Process CSV file here, e.g. $csvService->importFile($csvRows, $fileUpload);
                $this->addFlash('success', 'CSV file processed successfully.');

                // Processar recebimento

            } catch (FileException $e) {
                $this->addFlash('error', 'Failed to process the CSV file.');
            }
        } else {
            $this->addFlash('error', 'Invalid file upload.');
        }

        return $this->render('admin/campaign/show-csv.html.twig', [
            'csvRows' => $csvRows,
            'campaign' => $campaign,
        ]);

    }

    #[Route('/{campaignId}/generatePromptStep5', name: 'app_admin_campaign_generate_prompt_step5', methods: ['POST'])]
    public function getPromptStep5(Request $request, $campaignId, CampaignRepository $campaignRepository, PostRepository $postRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $campaign = $campaignRepository->find($campaignId);
        // Get the JSON content from the request
        $data = json_decode($request->getContent(), true);

        // localizar quem é o registro que será alterado
        $postId = $data['lineId'];
        $postDay = $data['data']['postid'];
        $postMonth = $data['data']['postmonth'];
        $postTextToPublish = $data['data']['posttexttopublish'];
        $posttitle = $data['data']['posttitle'];
        $posttext = $data['data']['posttext'];



        $post = $postRepository->find($postId);
        $post->setTextToPublish($postTextToPublish);

        $entityManager->persist($post);
        $entityManager->flush();

        $posttexts = $post->getPostTexts();
        foreach ($posttexts as $index => $text) {
            $text->setContent($posttext[$index]);
            $entityManager->persist($text);
            $entityManager->flush();
        }


        // Return a success response
        return new JsonResponse([
            'success' => true,
            'message' => 'Data received successfully',
            'campaignId' => $campaignId,
            'receivedData' => $data
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/{campaignId}/generatePromptStep6', name: 'app_admin_campaign_generate_prompt_step6', methods: ['POST'])]
    public function getPromptStep6(Request $request, $campaignId, CampaignRepository $campaignRepository): JsonResponse
    {
        $campaign = $campaignRepository->find($campaignId);
        exit;






    }


}
