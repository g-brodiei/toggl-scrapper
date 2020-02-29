<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TogglApiController extends AbstractController
{
    /**
     * @Route("/toggl/api", name="toggl_api")
     */
    public function index()
    {
        return $this->render('toggl_api/index.html.twig', [
            'controller_name' => 'TogglApiController',
        ]);
    }
}
