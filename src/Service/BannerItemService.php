<?php

namespace App\Service;

use App\Entity\BannerItem;
use App\Entity\BannerStructure;
use App\Entity\ErrorLog;
use App\Entity\Post;
use App\Repository\CampaignRepository;
use App\Repository\ErrorLogRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use GDText\Box;
use GDText\Color;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class BannerItemService
{
    private string $cacheDirectory = '../var/cache/structure/';
    public function __construct(
        private EntityManagerInterface $em,
        private PostRepository $postRepository,
        private CampaignRepository $campaignRepository,
        private ParameterBagInterface $parameterBag,
    )
    {
    }

    public function getTextContent(BannerStructure $structure)
    {

        // Montar array de conteúdos
        $textContent = array();
        foreach ($structure->getBannerTexts() as $index => $bannerText) {
            foreach ($bannerText->getBannerItemTexts() as $index => $postItemText) {
                //dump($postItemText);
                $textContent[$postItemText->getBannerText()->getTitle()][$postItemText->getBannerItem()->getId()] = $postItemText->getContent();
            }
        }


        // mixar conteúdos
        $newContent = array();
        foreach ($textContent as $index1 => $column) {
            foreach ($column as $index2 => $row) {
                $newContent[$index2][$index1] = $row;
            }
        }

        return $newContent;

    }


    public function getTextTitles(BannerStructure $structure)
    {
        // Montar array de títulos
        $textTitles = array();
        foreach ($structure->getBannerTexts() as $index => $bannerText) {
            $textTitles[] = $bannerText->getTitle();
        }
        return $textTitles;


    }

    public function getImageDataFromPost(Post $post)
    {
        //$post = $this->postRepository->find($post->getId());
        $campaign = $this->campaignRepository->find($post->getCampaign()->getId());
        if ($campaign->getBaseArtFile() == null){
            $teste = $campaign->getName();
            if ($campaign->getBaseArtFile() == null){
                echo 'Não consigo achar o path do BaseArt';
                exit;
            }
        }
        $leftMargin = 0;
        $topMargin = 0;

        if ($post->getBackgroundImageMarginX() > 0 || $post->getBackgroundImageMarginY() > 0){
            $leftMargin = $post->getBackgroundImageMarginX();
            $topMargin = $post->getBackgroundImageMarginY();
        } else {
            $leftMargin = $post->getCampaign()->getBannerWidth();
            $topMargin = $post->getCampaign()->getBannerHeight();
        }

        $newImage = $this->createImage($post->getimageFile()->getPathname(),$campaign->getBaseArtFile()->getPathname(),$post->getArrayTexts(), '../public/postimages/',$topMargin, $leftMargin);
        $oldImage = $post->getDinamicImage();
        $post->setDinamicImage($newImage);
        $this->em->persist($post);
        $this->em->flush();

        if ($oldImage){
            // apagar a imagem antiga
            $filesystem = new Filesystem();
            $oldImage = '../public/'.$oldImage;
            // Verifique se o arquivo existe
            if ($filesystem->exists($oldImage)) {
                // Remove o arquivo
                $filesystem->remove($oldImage);
            }
        }

        return $newImage;

    }


    public function createImage(string $backgroundImagePath, string $frontImagePath, array $texts, string $outputDirectory = '../public/postimages/', int $topMargin = 0, int $leftMargin = 0): string
    {

        $projectDir = $this->parameterBag->get('kernel.project_dir');
        $backgroundImagePath = str_replace($projectDir,'..',$backgroundImagePath);
        $frontImagePath = str_replace($projectDir,'..',$frontImagePath);

        $filesystem = new Filesystem();
        $convertedImagePath = $this->convertImageToPng($backgroundImagePath);

        $imageBackground = imagecreatefrompng($convertedImagePath);
        $imageFront = imagecreatefrompng($frontImagePath);

        list($imageBackground, $imageFront) = $this->resizeImages($imageBackground, $imageFront);

        $resultImage = $this->mergeImages($imageBackground, $imageFront, $topMargin, $leftMargin);

        $this->addTextsToImage($resultImage, $texts);

        $outputPath = $this->saveImage($resultImage, $outputDirectory);

        imagedestroy($resultImage);
        imagedestroy($imageBackground);
        imagedestroy($imageFront);
        $outputPath = str_replace('../public/','',$outputPath);
        return $outputPath;
    }

    private function convertImageToPng(string $imagePath): string
    {
        $imageInfo = getimagesize($imagePath);
        if ($imageInfo === false) {
            throw new \Exception("File is not a valid image: $imagePath");
        }

        $imageType = $imageInfo[2];
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($imagePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_WEBP:
                if (!function_exists('imagecreatefromwebp')) {
                    throw new \Exception('WebP support is not enabled in GD library');
                }
                $image = imagecreatefromwebp($imagePath);
                break;
            default:
                throw new \Exception("Unsupported image type: $imagePath");
        }

        $convertedImagePath = $this->cacheDirectory . 'tempImage.png';
        if (!imagepng($image, $convertedImagePath)) {
            throw new \Exception("Failed to save the converted image as PNG: $convertedImagePath");
        }

        imagedestroy($image);

        return $convertedImagePath;
    }

    private function resizeImages($imageBackground, $imageFront)
    {
        $backgroundWidth = imagesx($imageBackground);
        $backgroundHeight = imagesy($imageBackground);
        $frontWidth = imagesx($imageFront);
        $frontHeight = imagesy($imageFront);

        $newWidth = $frontWidth;
        $newHeight = ($backgroundHeight * $frontWidth) / $backgroundWidth;

        if ($newHeight < $frontHeight) {
            $newHeight = $frontHeight;
            $newWidth = ($backgroundWidth * $frontHeight) / $backgroundHeight;
        }

        $resizedBackground = imagecreatetruecolor($newWidth, $newHeight);
        imagesavealpha($resizedBackground, true);
        $transparentColor = imagecolorallocatealpha($resizedBackground, 0, 0, 0, 127);
        imagefill($resizedBackground, 0, 0, $transparentColor);
        imagecopyresampled($resizedBackground, $imageBackground, 0, 0, 0, 0, $newWidth, $newHeight, $backgroundWidth, $backgroundHeight);

        imagedestroy($imageBackground);
        $imageBackground = $resizedBackground;

        return [$imageBackground, $imageFront];
    }

    private function mergeImages($imageBackground, $imageFront, int $topMargin = 0, int $leftMargin = 0)
    {

        $backgroundWidth = imagesx($imageBackground);
        $backgroundHeight = imagesy($imageBackground);
        $frontWidth = imagesx($imageFront);
        $frontHeight = imagesy($imageFront);

        $resultImage = imagecreatetruecolor($frontWidth, $frontHeight);
        imagesavealpha($resultImage, true);
        $transparentColor = imagecolorallocatealpha($resultImage, 0, 0, 0, 127);
        imagefill($resultImage, 0, 0, $transparentColor);

        imagecopy($resultImage, $imageBackground, $leftMargin, $topMargin,0, 0, $backgroundWidth, $backgroundHeight);
        imagecopy($resultImage, $imageFront, 0, 0, 0, 0, $frontWidth, $frontHeight);

        return $resultImage;
    }

    private function addTextsToImage($image, array $texts)
    {
        foreach ($texts as $text) {
            $box = new Box($image);
            $box->setFontFace($text['font']);
            $box->setFontColor(new Color($text['color'][0], $text['color'][1], $text['color'][2]));
            $box->setTextShadow(new Color(0, 0, 0, 50), $text['shadow'][0], $text['shadow'][1]);

            $fontSize = $text['fontSize'];
            $textSize = strlen(trim($text['content']));
            if ($textSize > 60) {
                $fontSize -= ($textSize - 60) * 0.25;
            }
            $box->setFontSize($fontSize);

            $box->setLineHeight($text['lineHeight']);
            $box->setBox($text['box'][0], $text['box'][1], $text['box'][2], $text['box'][3]);
            $box->setTextAlign($text['align'][0], $text['align'][1]);
            $box->draw(trim($text['content']));
        }
    }

    private function saveImage($image, string $outputDirectory): string
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($outputDirectory)) {
            $filesystem->mkdir($outputDirectory, 0777);
        }

        $filename = 'banner-' . uniqid() . '.png';
        $outputPath = $outputDirectory . $filename;

        if (!imagepng($image, $outputPath)) {
            throw new \Exception("Failed to save the image: $outputPath");
        }

        return $outputPath;
    }
}