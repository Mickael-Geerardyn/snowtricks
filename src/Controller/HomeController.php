<?php

namespace App\Controller;

use App\Repository\FigureRepository;
use Exception;
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
		//dd($figureRepository->find(1));

		try
		{
			return $this->render('home/homepage.html.twig', [
				'figuresObjectsArray' => $figureRepository->findAll(),
			]);
		} catch (Exception $exception)
		{
			return $this->render("home/homepage.html.twig", [
				'danger' => $exception->getMessage(),
			]);
		}

    }
}
