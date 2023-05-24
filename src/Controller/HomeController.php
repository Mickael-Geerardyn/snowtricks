<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
	/**
	 * @param FigureRepository $figureRepository
	 *
	 * @return Response
	 */
    #[Route('/', name: 'home')]
    public function home(FigureRepository $figureRepository): Response
    {
        return $this->render('home/homepage.html.twig', [
			"figure" => $figureRepository->findAll(),
		]);
    }
}
