<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Image;
use App\Entity\Message;
use App\Entity\Video;
use App\Form\CommentFormType;
use App\Form\FigureFormType;
use App\Repository\ImageRepository;
use App\Service\DateTime;
use App\Service\FilesService;
use App\Service\PaginatorService;
use App\Service\SluggerService;
use App\Service\VideoRegex;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use function PHPUnit\Framework\throwException;

class FigureController extends AbstractController
{
	#[Route("/figure/new", name: 'app_figure_create')]
	public function createFigure(
		Request $request,
		EntityManagerInterface $entityManager,
		SluggerInterface $slugger,
		SluggerService $sluggerService,
		VideoRegex $videoRegex
	): Response
	{
		try {
			$figure = new Figure();

			$form = $this->createForm(FigureFormType::class, $figure);

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {

				$bannerFile = $form->get("banner")->getData();

				$originalBannerFilename = pathinfo($bannerFile->getClientOriginalName(), PATHINFO_FILENAME);

				$safeBannerFilename = $slugger->slug($originalBannerFilename);
				$newBannerFilename  = $safeBannerFilename . '-' . uniqid() . '.' . $bannerFile->guessExtension();

				try {
					$bannerFile->move(
						$this->getParameter('images_directory'),
						$newBannerFilename
					);

					$imageEntity = new Image();
					$imageEntity->setPath($newBannerFilename);
					$imageEntity->setUser($this->getUser());
					$imageEntity->setBanner(true);

					$figure->addImage($imageEntity);

				} catch (Exception $exception)
				{
					$this->addFlash("error", $exception->getMessage());

					$this->redirectToRoute("app_figure_create");
				}

				// File upload part
				$uploadedFiles = $form->get('image')->getData();

				if($uploadedFiles)
				{
						foreach ($uploadedFiles as $uploadedFile) {

							$originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);

							$safeFilename = $slugger->slug($originalFilename);
							$newFilename  = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

							// Add try-catch to catch if an error occurs during moving file in the storage directory
							try {

								$uploadedFile->move(
									$this->getParameter('images_directory'),
									$newFilename
								);

							} catch (FileException $e) {

								$this->addFlash("error", $e->getMessage());

								$this->redirectToRoute("app_figure_create");
							}

							$imageEntity = new Image();
							$imageEntity->setPath($newFilename);
							$imageEntity->setUser($this->getUser());
							$imageEntity->setBanner();

							$figure->addImage($imageEntity);
						}
				}

				// Upload video string path part
				$videoPath = $form->get("video")->getData();

				$result = $videoRegex->getVideoUrl($videoPath);

				if(!$result)
				{
					$this->addFlash("error", "Une erreur est survenue, veuillez réessayer");

					return $this->redirectToRoute("home");
				}

					$videoEntity = new Video();
					$videoEntity->setPath($result[0]);

					$figure->addVideo($videoEntity);

				$figureName = $figure->getName();
				$sluggedName = $sluggerService->makeSlug($figureName);

				$figure->setSlug($sluggedName);
				$figure->setCreatedAt();

				$figure->setUser($this->getUser());

				$entityManager->persist($figure);
				$entityManager->flush();

				$this->addFlash("success", "La figure a bien été créée");

				return $this->redirectToRoute("home");
			}

			return $this->render("figure/create.html.twig", [
				"figureForm" => $form->createView(),
			]);

		} catch (Exception $exception)
		{
			$this->addFlash("error", $exception->getMessage());

			return $this->redirectToRoute("home");
		}

	}

	#[Route("/figure/delete/{slug}", name: 'app_figure_delete')]
	public function deleteFigure(
		Figure $figure,
		EntityManagerInterface $entityManager,
	): Response
	{
		try {

			$images = $figure->getImages();

			foreach($images as $image)
			{
				$projectDir = $this->getParameter("images_directory");

				$fileSystem = new Filesystem();

				$fileSystem->remove($projectDir ."/" . $image->getPath());
			}

			$entityManager->remove($figure);
			$entityManager->flush();

			$this->addFlash("success", "La figure a bien été supprimée");

			return $this->redirectToRoute("home");

		} catch (Exception $exception) {

			$this->addFlash("error", "Une erreur est intervenue, veuillez réessayer");

			return $this->redirectToRoute("home");
		}
	}

	#[Route("/figure/update/{slug}", name: 'app_figure_update')]
	public function updateFigure(
		Figure $figure,
		Request $request,
		EntityManagerInterface $entityManager,
		SluggerInterface $slugger,
		SluggerService $sluggerService,
		DateTime $dateTime,
		VideoRegex $videoRegex,
		FilesService $filesService,
		ImageRepository $imageRepository
	): Response
	{
		try {

			$form = $this->createForm(FigureFormType::class, $figure);

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid())
			{

				$uploadBanner = $form->get('banner')->getData();

				if($uploadBanner)
				{
					$originalFilename = $filesService->getOriginalFileName($uploadBanner);

					$safeFilename = $slugger->slug($originalFilename);
					$newBannerFilename = $filesService->getNewFileName($safeFilename, $uploadBanner);

					// Add try-catch to catch if an error occurs during moving file in the storage directory
					try {

						$uploadBanner->move(
							$this->getParameter('images_directory'),
							$newBannerFilename
						);

					} catch (FileException $exception) {

						$this->addFlash("error", "Une erreur est intervenue, veuillez réessayer");

						$this->redirectToRoute("app_figure_create");
					}

					$images = $figure->getImages()->getValues();
					foreach($images as $image)
					{
						if($image->isBanner())
						{
							$image->setBanner();
							$entityManager->persist($image);
						}
					}

					$imageEntity = new Image();
					$imageEntity->setPath($newBannerFilename);
					$imageEntity->setBanner(true);
					$imageEntity->setUser($this->getUser());

					$figure->addImage($imageEntity);
				}

				// File upload part
				$uploadedFiles = $form->get('image')->getData();

				if($uploadedFiles)
				{
					foreach($uploadedFiles as $file)
					{
						$originalFilename = $filesService->getOriginalFileName($file);

						$safeFilename = $slugger->slug($originalFilename);
						$newFilename = $filesService->getNewFileName($safeFilename, $file);

						// Add try-catch to catch if an error occurs during moving file in the storage directory
						try {

							$file->move(
								$this->getParameter('images_directory'),
								$newFilename
							);

						} catch (FileException $exception) {

							$this->addFlash("error", "Une erreur est intervenue, veuillez réessayer");

							$this->redirectToRoute("app_figure_create");
						}
						$imageEntity = new Image();
						$imageEntity->setPath($newFilename);
						$imageEntity->setUser($this->getUser());

						$figure->addImage($imageEntity);
					}

				}

				// Upload video string path part
				$videoPath = $form->get("video")->getData();

				if($videoPath)
				{
					$result = $videoRegex->getVideoUrl($videoPath);

					if(!$result)
					{
						throw new Exception("Veuillez renseigner l'url du bouton 'intégrer' de partage youtube.");
					}

					$videoEntity = new Video();
					$videoEntity->setPath($result[0]);
					$videoEntity->setUser($this->getUser());

					$figure->addVideo($videoEntity);
				}

				$figureName = $figure->getName();
				$sluggedName = $sluggerService->makeSlug($figureName);

				$figure->setSlug($sluggedName);
				$figure->setUpdatedAt($dateTime->getDateTime());

				$figure->setUser($this->getUser());

				$entityManager->persist($figure);
				$entityManager->flush();

				$this->addFlash("success", "La figure a bien été modifiée");

				return $this->redirectToRoute("home");
			}


			return $this->render("figure/update.html.twig", [
				"figureForm" => $form->createView(),
				"figure" => $figure
			]);

		} catch (Exception $exception)
		{
			$this->addFlash("error", $exception->getMessage());

			return $this->redirectToRoute("home");
		}
	}

    #[Route('/figure/{slug}', name: 'app_figure')]
    public function showFigure(
		Figure $figure,
		Request $request,
		EntityManagerInterface $entityManager,
		PaginatorService $paginatorService
	): Response
	{
		$totalPages = $paginatorService->getTotalPagesPerFigure($figure);

		$currentPage = $paginatorService->getFinalPage($request, $totalPages);

		$comments = $paginatorService->getCurrentPageComments($figure, $currentPage);

		$message = new Message();

		$form = $this->createForm(CommentFormType::class, $message);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$figure->addMessage($message);
			$message->setUser($this->getUser());

			$entityManager->persist($figure);
			$entityManager->flush();

			$this->addFlash("success", "Votre commentaire a bien été enregistré");

			return $this->redirectToRoute("home");
		}

        return $this->render('figure/index.html.twig', [
            'figure' => $figure,
			'form'=> $form->createView(),
			'comments' => $comments,
			'totalPages' => $totalPages,
			'currentPage' => $currentPage
        ]);
    }
}
