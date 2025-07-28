<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Entity\PostText;
use App\Repository\BannerItemRepository;
use App\Repository\BannerItemTextRepository;
use App\Repository\BannerStructureRepository;
use App\Repository\CampaignRepository;
use App\Repository\CampaignStructureRepository;
use App\Repository\PostRepository;
use App\Service\BannerItemService;
use Doctrine\ORM\EntityManagerInterface;
use GDText\Box;
use GDText\Color;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BannerItemTextController extends AbstractController
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    #[Route('/admin/{structureId}/banner/item/text', name: 'app_admin_banner_item_text')]
    public function index($structureId, BannerStructureRepository $bannerStructureRepository, BannerItemService $bannerItemService): Response
    {
        $structure = $bannerStructureRepository->find($structureId);

        return $this->render('admin/banner_item_text/index.html.twig', [
            'structure' => $bannerItemService->getTextContent($structure),
            'textTitles' => $bannerItemService->getTextTitles($structure),
            'menuActive' => 'banner_structure',
        ]);
    }

    #[Route('/admin/banner/item/image/{postId}', name: 'app_admin_banner_item_image')]
    public function image( BannerItemService $bannerItemService, $postId, PostRepository $postRepository, CampaignRepository $campaignRepository, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): Response
    {


        $post = $postRepository->find($postId);
//        $campaign = $campaignRepository->find($post->getCampaign()->getId());
//        if ($campaign->getBaseArtFile() == null){
//            $teste = $campaign->getName();
//            if ($campaign->getBaseArtFile() == null){
//                echo 'Não consigo achar o path do BaseArt';
//                exit;
//            }
//        }
//
//
//
//        $newImage = $bannerItemService->createImage($post->getimageFile()->getPathname(),$campaign->getBaseArtFile()->getPathname(),$post->getArrayTexts());

        $newImage = $bannerItemService->getImageDataFromPost($post);
        $imageData = file_get_contents('../public/'.$newImage);

        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        $response->headers->set('Content-Disposition', 'inline; filename="banner-item-' . $postId . '.png"');
        $response->setContent($imageData);

        return $response;


    }
    #[Route('/admin/banner/item/imageTempFromCampaign/{campaignId}', name: 'app_admin_image_temp_from_structure')]
    public function imageTemp( BannerItemService $bannerItemService, $campaignId, CampaignRepository $campaignRepository, EntityManagerInterface $entityManager, PostRepository $postRepository): Response
    {
        // criar um post temporário com textos temporários
        $campaign = $campaignRepository->find($campaignId);

        //$post = $this->postRepository->find($post->getId());
        if ($campaign->getBaseArtFile() == null){
            $teste = $campaign->getName();
            if ($campaign->getBaseArtFile() == null){
                echo 'Não consigo achar o path do BaseArt';
                exit;
            }
        }
        $leftMargin = 0;
        $topMargin = 0;
        $texts = [];
        foreach ($campaign->getCampaignStructures() as $structure) {
            $texts[] = [
                'content' => $structure->getTitle(),
                'font' => $structure->getFont(),
                'color' => [$structure->getColorR(), $structure->getColorG(), $structure->getColorB()], // RGB color
                'shadow' => [$structure->getShadowXShift(), $structure->getShadowYShif()], // Shadow X and Y offset
                'fontSize' => $structure->getFontSize(),
                'lineHeight' => $structure->getLineHeight(),
                'box' => [$structure->getBoxX(), $structure->getBoxY(), $structure->getBoxWidth(), $structure->getBoxHeight()], // X, Y, Width, Height
                'align' => [$structure->getAlignX(), $structure->getAlignY()] // Horizontal and vertical alignment
            ];
        }

        $newImage = $bannerItemService->createImage('../public/images/imageImage/temp-back-1080x1080.png',$campaign->getBaseArtFile()->getPathname(),$texts, '../var/cache/',$topMargin, $leftMargin);
        $imageData = file_get_contents('../public/'.$newImage);

        $response = new Response();
        $response->headers->set('Content-Type', 'image/png');
        $response->headers->set('Content-Disposition', 'inline; filename="banner-item-' . uniqid() . '.png"'); // Improved filename
        $response->setContent($imageData); // Set the image data as the response content



        return $response;
    }
}
