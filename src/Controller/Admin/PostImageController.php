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
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/post-image')]
class PostImageController extends AbstractController
{
    #[Route('/{id}', name: 'app_admin_post_image_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        $texts = $post->getPostTexts();
        $title = $texts[0]->getContent().' - '.$post->getCampaign()->getClient()->getActivityType().' '.$post->getCampaign()->getClient()->getCompanyType();
        return $this->render('admin/post/form-to-search.html.twig', [
            'post' => $post,
            'title' => $title,
        ]);
    }

    #[Route('/{id}/choice', name: 'app_admin_post_image_choice', methods: ['GET'])]
    public function choice(Request $request, Post $post, UnsplashService $unsplashService): Response
    {
        $search = $request->get('search');
        $images = $unsplashService->searchImages($search,4);
//        dump($images);
        return $this->render('admin/post/show.html.twig', [
            'post' => $post,
            'images' => $images,
        ]);
    }

    #[Route('/{id}/setImage', name: 'app_admin_post_set_image', methods: ['GET'])]
    public function setImage(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        $url = $request->get('url');


        // Baixar imagem da URL
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $url);
        if ($response->getStatusCode() !== 200) {
            return new Response('Failed to download image', Response::HTTP_BAD_REQUEST);
        }
        $imageData = $response->getContent();

        // Gerar novo nome para a imagem
        $imageExtension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
        if ($imageExtension == ''){
            $imageExtension = '.png';
        }
        $newImageName = uniqid() . '.' . $imageExtension;

        // Salvar imagem na pasta public/images
        $publicDir = $this->getParameter('kernel.project_dir') . '/public/images/imageImage/';
        file_put_contents($publicDir . $newImageName, $imageData);

        // Atualizar entidade Post com o novo nome da imagem
        $post->setImage($newImageName);
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_campaign_step5_post_edit', ['campaignId' => $post->getCampaign()->getId(),'id' => $post->getId()], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/reposition', name: 'app_admin_post_image_reposition', methods: ['GET'])]
    public function setImageReposition(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        return $this->render('admin/post/reposition.html.twig', [
            'post' => $post,
        ]);
    }
    #[Route('/{id}/reposition-new', name: 'app_admin_post_image_reposition_new', methods: ['GET','POST'])]
    public function setImageRepositionNew(Request $request, Post $post, EntityManagerInterface $entityManager, BannerItemService $bannerItemService): Response
    {
        $marginY = $request->get('marginY');
        $marginX = $request->get('marginX');
        $post->setBackgroundImageMarginX($marginX);
        $post->setBackgroundImageMarginY($marginY);
        $entityManager->persist($post);
        $entityManager->flush();
        $img = $bannerItemService->getImageDataFromPost($post);

        return $this->redirectToRoute('app_admin_campaign_step5_post_edit', ['campaignId' => $post->getCampaign()->getId(),'id' => $post->getId()], Response::HTTP_SEE_OTHER);
    }


}