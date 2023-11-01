<?php

// src/Controller/PublishController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;

class PublishController extends AbstractController
{
    #[Route('/publish')]  
    public function publish(HubInterface $hub): Response
    {
        $update = new Update(
            '#time',
            json_encode(['time' => date('Y-m-d H:i:s')])
        );

        $hub->publish($update);

        return new Response('published!');
    }
}