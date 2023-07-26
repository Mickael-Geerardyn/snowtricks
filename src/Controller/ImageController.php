<?php

namespace App\Controller;

use App\Entity\Image;
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

class ImageController extends AbstractController
{
	#[Route('/image/update/{id}', name: 'app_image_update')]
	public function updateImage
	(
		Image            $image,
		Request          $request,
		SluggerInterface $slugger,
		EntityManagerInterface $entityManager,

	): Response
	{
		$form = $this->createForm(ImageFormType::class);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{

			// File upload part
			$uploadedFile = $form->get('path')->getData();

			if ($uploadedFile) {

				$projectDir = $this->getParameter("images_directory");
				$fileSystem = new Filesystem();

				$fileSystem->remove($projectDir ."/" . $image->getPath());

				$originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);

				$safeFilename = $slugger->slug($originalFilename);
				$newFilename  = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

				$image->setPath($newFilename);
				$image->setUser($this->getUser());

				// Add try-catch to catch if an error occurs during moving file in the storage directory
				try {

					$uploadedFile->move(
						$this->getParameter('images_directory'),
						$newFilename
					);

				} catch (FileException $exception) {

					$this->addFlash("error", "Une erreur est intervenue, veuillez réessayer");

					$this->redirectToRoute("app_figure");
				}

				$entityManager->persist($image);
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
