<?php

// src/Controller/FacebookController.php

namespace App\Controller\Admin;

use Facebook\Facebook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FacebookController extends AbstractController
{

     #[Route('/admin/post-to-facebook', name: 'post_to_facebook')]
    public function postToFacebook(): JsonResponse
    {
// https://developers.facebook.com/tools/explorer/436608939179494/?method=GET&path=wabagenciadigital%20%2Fposts&version=v20.0
         // page id 124468460996517
        $fb = new Facebook([
            'access_token'=> 'EAAGNFZCC5veYBO6A7b8IYr9XXqfPJfTiqrKk93kAjdn0kVDACogOPQ9IaO1K2G6zWj9t0JinJROAkvqbLPiljW2SzCZASUkMOZAXCZBy4A8d0MKJZAUCEckydnYNcdYhOv5v8ZC88TsYfxVpt8D5N9yspHNq6iP7ZAAYZAHqMZBriTOhUtdKy0SF73Kvg1OaZAr7LVhYG81J77HD4ex3jr71YoLUbFp1MZC82NbhQZDZD',
            'app_id' => '436608939179494',
            'app_secret' => '6cbcb98b9190ed510449438f32e0eecc',
            'default_graph_version' => 'v20.0',
        ]);

        $accessToken = 'EAAGNFZCC5veYBO6A7b8IYr9XXqfPJfTiqrKk93kAjdn0kVDACogOPQ9IaO1K2G6zWj9t0JinJROAkvqbLPiljW2SzCZASUkMOZAXCZBy4A8d0MKJZAUCEckydnYNcdYhOv5v8ZC88TsYfxVpt8D5N9yspHNq6iP7ZAAYZAHqMZBriTOhUtdKy0SF73Kvg1OaZAr7LVhYG81J77HD4ex3jr71YoLUbFp1MZC82NbhQZDZD';

        $link = 'https://127.0.0.1:8000/admin/banner/item/image/155';
        $message = 'Esta é uma postagem de testes';
        $scheduledPublishTime = strtotime('2024-07-14 20:00:00');

        $data = [
            'message' => $message,
            'link' => $link,
            'published' => false, // false to schedule the post
            'scheduled_publish_time' => $scheduledPublishTime,
        ];

        try {
            $response = $fb->post('/me/feed', $data, $accessToken);
            $graphNode = $response->getGraphNode();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            return new JsonResponse(['error' => 'Graph returned an error: ' . $e->getMessage()], 400);
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return new JsonResponse(['error' => 'Facebook SDK returned an error: ' . $e->getMessage()], 400);
        }

        return new JsonResponse(['success' => 'Post ID: ' . $graphNode['id']]);
    }
}
