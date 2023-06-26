<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\CommentFormType;
use App\Repository\FigureRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FigureController extends AbstractController
{
    #[Route('/figure/{slug}', name: 'app_figure')]
    public function index(string $slug, FigureRepository $figureRepository, MessageRepository $messageRepository ,Request $request, EntityManagerInterface $entityManager): Response
    {
		$message = new Message();
		$figure = $figureRepository->findOneBy(["slug" => $slug]);
		$form = $this->createForm(CommentFormType::class, $message);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$message = $form->getData();
			$figure->addMessage($message);
			dd($message, $figure->getMessages()->getValues());
		}

        return $this->render('figure/index.html.twig', [
            'figure' => $figure,
			'form'=> $form->createView(),
        ]);
    }

	#[Route("/figure/{slug}", name: 'app_figure_update')]
	public function update(string $slug, FigureRepository $figureRepository)
	{


	}
}
