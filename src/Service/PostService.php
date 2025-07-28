<?php

namespace App\Service;

use App\Entity\BannerItem;
use App\Entity\BannerStructure;
use App\Entity\Campaign;
use App\Entity\Client;
use App\Entity\ErrorLog;
use App\Entity\Post;
use App\Entity\PostHistory;
use App\Repository\ErrorLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use GDText\Box;
use GDText\Color;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PostService
{
    public function __construct(
        private EntityManagerInterface $em,
        private readonly Security $security,
        private HttpClientInterface $httpClient,
        private UrlGeneratorInterface $urlGenerator,
        private BannerItemService $bannerItemService
    )
    {
    }

    public function schedulePost(Post $post)
    {

        date_default_timezone_set('America/Sao_Paulo');
        if ($post->getApprovedBy() == null){
            return false;
        }

        if ($post->getFacebookId() !== null && $post->getFacebookId() !== ""){
            return false;
        }

        $token = $this->security->getUser()->getFacebookAccessToken();
        if ($token == null || $token == ""){
            return false;
        }

        // URL relativa
        //$relativeUrl = $this->urlGenerator->generate('app_admin_banner_item_image', ['postId' => $post->getId()]);

        // URL absoluta (full URL)
        $absoluteUrl = $this->urlGenerator->generate('app_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);

        //$file = $this->bannerItemService->getImageData($post,'../','filename');
        $file = $post->getDinamicImage();
        $fullFileName = $absoluteUrl.$file;

        $url = 'https://graph.facebook.com/v20.0/'.$post->getCampaign()->getClient()->getFacebookPageId().'/photos';
        $params = [
            'url' => $fullFileName,
            'scheduled_publish_time' => $post->getScheduleDate()->getTimestamp(),
            'published' => 'false',
            'caption' => strip_tags($post->getTextToPublish()),
            'access_token' => $token
        ];
        $result = $this->apiFacebookUrl($url,$params,'POST');
//        dump($result);

        //$data =  new JsonResponse($result);
        $data =  $result;
//        dump($data);

        if ($data['success'] == true){
//            dump('$data[\'success\'] == true');
            if (isset($data['id'])){
//                dump('isset($data[\'id\']');
                $post->setFacebookId($data['id']);
                $this->em->persist($post);
                $this->em->flush();

                $postHistory = new PostHistory();
                $postHistory->setPost($post);
                $postHistory->setEventDescription('Post insert in Facebook');
                $postHistory->setOccurredIn(new \DateTime());
                $this->em->persist($postHistory);
                $this->em->flush();
            }
        }


        if ($data['success'] == false){
            $message = 'Erro ao enviar para o Facebook. '.$data['error_message'].' '.$data['error']['error_user_title'].' '.$data['error']['error_user_msg'];
            //$this->container->get('request_stack')->getSession()->getFlashBag()->add('error', $message);
            $session = new Session();
            // retrieve the flash messages bag
            $flashes = $session->getFlashBag();

            // add flash messages
            $flashes->add(
                'error',
                $message
            );

            $postHistory = new PostHistory();
            $postHistory->setPost($post);
            $postHistory->setEventDescription($message);
            $postHistory->setOccurredIn(new \DateTime());
            $this->em->persist($postHistory);
            $this->em->flush();

        }

        return $data;

    }

    public function removeSchedulePost(Post $post)
    {

        if ($post->getFacebookId() == null OR $post->getFacebookId() == ""){
            echo 'sem getFacebookId';
            return false;
        }

        $token = $this->security->getUser()->getFacebookAccessToken();
        if ($token == null || $token == ""){
            echo 'sem getFacebookAccessToken';
            return false;
        }

        $url = 'https://graph.facebook.com/v20.0/'.$post->getFacebookId();

       // $url = 'https://graph.facebook.com/v20.0/'.$post->getCampaign()->getClient()->getFacebookPageId().'/photos';
        $params = [
            'access_token' => $token
        ];
        $result = $this->apiFacebookUrl($url,$params,'DELETE');
        //dump($result);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        echo 'HTTP Code: ' . $httpCode . PHP_EOL;
        echo 'Response: ' . $response . PHP_EOL;

        // Criando evento
        $postHistory = new PostHistory();
        $postHistory->setPost($post);
        $postHistory->setEventDescription('Removido do Facebook');
        $postHistory->setOccurredIn(new \DateTime());
        $this->em->persist($postHistory);
        $this->em->flush();

        // Removendo ID
        $post->setFacebookId(null);
        $this->em->persist($post);
        $this->em->flush();

    }

    public function getHistoryPost(Client $client)
    {

        date_default_timezone_set('America/Sao_Paulo');

        $token = $this->security->getUser()->getFacebookAccessToken();
        if ($token == null || $token == ""){
            return false;
        }

        //https://graph.facebook.com/v20.0/124468460996517/posts?limit=100&fields=id%2Cmessage%2Ccreated_time&access_token=EAAGNFZCC5veYBO8Iu9rzNlbcAqiEggqfrhpCXfPXVBhaZCG1N5hvNeC0KWDRUMXAjxC0pD69L9hn6zosm4XT6Dx9vLuI1v4eMkFZBLIutJj07p3ZBOFzHz24ZAFUN1rPqRCRvrfxbXbtvDaMDmtkSw76Gbs4MsbSKzXj9ZAONn0KbARKBlNSqgvATODz7HjxTUzZAO9rjRZAYnK2lZCoZD
        $url = 'https://graph.facebook.com/v20.0/'.$client->getFacebookPageId().'/posts?limit=100&fields=id%2Cmessage%2Ccreated_time&access_token='.$token;
        //$url = 'https://graph.facebook.com/v20.0/'.$client->getFacebookPageId().'/posts?limit=100&fields=id,message,created_time?access_token='.$token;
        $result = $this->apiFacebookUrl($url,[],'GET');

        return $result;
    }

    public function getAllSchedulePost(Campaign $campaign)
    {

        date_default_timezone_set('America/Sao_Paulo');

        $token = $this->security->getUser()->getFacebookAccessToken();
        if ($token == null || $token == ""){
            return false;
        }


        $url = 'https://graph.facebook.com/v20.0/'.$campaign->getClient()->getFacebookPageId().'/scheduled_posts/?access_token='.$token;
        $result = $this->apiFacebookUrl($url,[],'GET');

        return $result;
    }


    public function apiFacebookUrl(string $url, array $params, string $type): array
    {
        try {
            $response = $this->httpClient->request($type, $url, [
                'body' => $params
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false);
            $data = json_decode($content, true);

            if ($statusCode === 200) {
                $id = $data['id'] ?? null;
                $data['success'] = true;
                return $data;
            }

            $errorMessage = $data['error']['message'] ?? 'Unknown error';
            $data['success'] = false;
            $data['error_message'] = $errorMessage;
            return $data;

        } catch (ClientExceptionInterface $e) {
            $response = $e->getResponse();
            $content = $response->getContent(false);
            $data = json_decode($content, true);

            return [
                'success' => false,
                'error' => 'Client error: ' . ($data['error']['message'] ?? $e->getMessage()),
                'error_code' => $data['error']['code'] ?? null,
                'error_subcode' => $data['error']['error_subcode'] ?? null,
                'error_user_title' => $data['error']['error_user_title'] ?? null,
                'error_user_msg' => $data['error']['error_user_msg'] ?? null,
                'fbtrace_id' => $data['error']['fbtrace_id'] ?? null,
                'data' => $data,
            ];
        } catch (RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            return [
                'success' => false,
                'error' => 'HTTP error: ' . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'General error: ' . $e->getMessage(),
            ];
        }
    }
}