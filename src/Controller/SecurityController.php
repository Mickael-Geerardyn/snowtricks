<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordPageFormType;
use App\Form\ResetPasswordPageFormType;
use App\Repository\UserRepository;
use App\Security\CustomTokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/forgot-password', name: 'app_password_forgot_page')]
    public function forgotPasswordPage(
		Request $request,
		CustomTokenGenerator $customTokenGenerator,
		MailerController $mailerController,
		UserRepository $userRepository,
		EntityManagerInterface $entityManager,
	): Response
    {
		try {

			$form = $this->createForm(ForgotPasswordPageFormType::class);

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid())
			{
				$user = $userRepository->findOneBy([
					'name' => $form->get("name")->getData(),
												   ]);

				if(!$user->isVerified() && !empty($user->getTokenValidator()))
				{
					return $this->render("home/homepage.html.twig", [
						'error' => "Veuillez valider votre compte à l'aide du lien envoyé lors de votre inscription avant de modifier votre mot de passe"
					]);
				}

				$validatorToken = $customTokenGenerator->getToken();

				$user->setTokenValidator($validatorToken);

				$entityManager->persist($user);
				$entityManager->flush();

				$mailerController->sendForgotPasswordMail($user->getEmail(), $user->getName(), $validatorToken);

				return $this->render('home/homepage.html.twig', ['success' => "Suivez les instructions envoyées à l'adresse email rattachée au nom d'utilisateur renseigné"]);
			};

			return $this->render("password/forgot-password.html.twig", [
				"forgotPasswordPageForm" => $form->createView(),
			]);

		}catch (Exception $exception){

			return $this->render("home/homepage.html.twig", [
				'error' => $exception->getMessage()
			]);
		}
    }

	#[Route('/reset-password?token={token}&email={userEmail}',
		name: 'app_password_reset_page',
		requirements: ['token' => ".+"],
		defaults: ["userEmail" => "userEmail"])]
	public function modifyPasswordPage(
		Request $request,
		CustomTokenGenerator $customTokenGenerator,
		MailerController $mailerController,
		UserRepository $userRepository,
		UserPasswordHasherInterface $userPasswordHasher,
		EntityManagerInterface $entityManager,
		string $token,
		string $userEmail
	)
	{
		try {

			$user = $userRepository->findOneBy([
				"email" => $userEmail,
												   "tokenValidator" => $token
											   ]);

			if (empty($user)) {
				return $this->render("home/homepage.html.twig", [
					"error" => "Une erreur est survenue, veuillez réessayer"
				]);
			}

			$form = $this->createForm(ResetPasswordPageFormType::class, $user);

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {

				if($form->get('password')->getData() !== $form->get('confirmationPassword')->getData())
				{
					return $this->render("home/homepage.html.twig", [
						"error" => "Une erreur est survenue, veuillez réessayer"
					]);
				}

				$user->setPassword(
					$userPasswordHasher->hashPassword(
						$user,
						$form->get('password')->getData()
					),
				);

				$user->setTokenValidator(null);

				$entityManager->persist($user);
				$entityManager->flush();

				return $this->render("home/homepage.html.twig", [
					"success" => "Votre mot de passe à bien été modifié"
				]);
			}

			return $this->render("password/reset-password.html.twig", [
				"resetFormType" => $form->createView(),
			]);

		}catch (Exception $exception){

			return $this->render("home/homepage.html.twig", [
				'error' => $exception->getMessage()
			]);
		}
	}
}
