<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage() 
    {
        return new Response('OMG, it\'s my new page');
    }

    /**
     * @Route("/news/{slug}")
     */
    public function show($slug) 
    {
        $comments = [
            'I ate a normal rock once. It did NOT taste like bacon!',
            'Woohoo! I\'m going on an all-asteroid diet!',
            'I like bacon too! Buy some from my site! bakinsomebacon.com',
        ];

        return $this->render('article/show.html.twig',[
            'title' => ucwords(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/news/{slug}/heart", name="article_toggle_heart", methods="POST")
     */
    public function toggleArticleHeart($slug, LoggerInterface $logger) {

        // TODO actually heart/unheart article!
        
        $logger->info('Article is being hearted');

        // return new Response(json_encode(['hearts' => 5]));

        return new JsonResponse(['hearts' => rand(5,100)]);
    }
}