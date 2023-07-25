<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Video;
use App\Form\ImageFormType;
use Doctrine\ORM\EntityManagerInterface;
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
	): Response
	{
		$form = $this->createForm(ImageFormType::class);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{


				$entityManager->persist($video);
				$entityManager->flush();
			}

			$this->addFlash("success", "L'image a bien été mise à jour");

			return $this->redirectToRoute("home");
		}

		return $this->render('image/update.html.twig', [
			'imageForm' => $form->createView(),
		]);
	}

	#[Route('/image/delete/{id}', name: 'app_image_delete')]
	public function deleteImage(
		Image $image,
		EntityManagerInterface $entityManager,
	):RedirectResponse
	{
		$projectDir = $this->getParameter("images_directory");
		$fileSystem = new Filesystem();

		$fileSystem->remove($projectDir ."/" . $image->getPath());

		$entityManager->remove($image);
		$entityManager->flush();

		$this->addFlash("success", "L'image a bien été supprimée");

		return $this->redirectToRoute("home");
	}
}
