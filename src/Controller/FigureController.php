<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureController extends AbstractController
{
    #[Route('/figure/{slug}', name: 'app_figure')]
    public function index(string $slug, FigureRepository $figureRepository): Response
    {
        return $this->render('figure/index.html.twig', [
            'figure' => $figureRepository->findOneBy(["slug" => $slug]),
        ]);
    }
}
