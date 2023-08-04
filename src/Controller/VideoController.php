<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Video;
use App\Form\ImageFormType;
use App\Form\VideoFormType;
use App\Service\VideoRegex;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class VideoController extends AbstractController
{
	#[Route('/video/update/{id}', name: 'app_video_update')]
	public function updateVideo
	(
		Video            $video,
		Request          $request,
		SluggerInterface $slugger,
		EntityManagerInterface $entityManager,
		VideoRegex $videoRegex

	): Response
	{
		$form = $this->createForm(VideoFormType::class, $video);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$videoPath = $form->get("path")->getData();

			$result = $videoRegex->getVideoUrl($videoPath);

			if(!$result)
			{
				$this->addFlash("error", "Une erreur est survenue, veuillez réessayer");

				return $this->redirectToRoute("home");
			}

			$video->setPath($result[0]);

				$entityManager->persist($video);
				$entityManager->flush();

			$this->addFlash("success", "La vidéo a bien été mise à jour");

			return $this->redirectToRoute("home");
		}

		return $this->render('video/update.html.twig', [
			'videoForm' => $form->createView(),
		]);
	}

	#[Route('/video/delete/{id}', name: 'app_video_delete')]
	public function deleteVideo(
		Video $video,
		EntityManagerInterface $entityManager,
	):RedirectResponse
	{
		try {

			$entityManager->remove($video);
			$entityManager->flush();

			$this->addFlash("success", "La vidéo a bien été supprimée");

			return $this->redirectToRoute("home");

		} catch (Exception $exception){

			$this->addFlash("error", $exception->getMessage());

			return $this->redirectToRoute("home");
		}
	}
}
