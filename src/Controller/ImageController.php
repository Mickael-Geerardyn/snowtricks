<?php

namespace App\Controller;

use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    #[Route('/image/update/{imageId}', name: 'app_image_update')]
    public function updateImage(Image $image): Response
    {
		

        return $this->render('image/index.html.twig', [
            'controller_name' => 'ImageController',
        ]);
    }
}
