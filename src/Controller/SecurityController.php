<?php

namespace App\Controller;

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
				$user = $userRepository->findOneBy
				([
					'name' => $form->get("name")->getData(),
					 ]);

				if (!$user)
				{
					$this->addFlash("error", "Cet utilisateur n'existe pas");

					return $this->redirectToRoute("app_password_forgot_page");
				}

				$validatorToken = $customTokenGenerator->getToken($user->getEmail());

				$user->setTokenValidator($validatorToken);

				$entityManager->persist($user);
				$entityManager->flush();

				$mailerController->sendForgotPasswordMail($user->getEmail(), $user->getName(), $validatorToken);

				$this->addFlash("success", "Suivez les instructions envoyées à l'adresse email rattachée au nom d'utilisateur renseigné");

				return $this->redirectToRoute("home");
			}

			return $this->render("password/forgot-password.html.twig", [
				"forgotPasswordPageForm" => $form->createView(),
			]);

		}catch (Exception $exception){

			$this->addFlash("error", $exception->getMessage());

			return $this->redirectToRoute("home");
		}
    }

	#[Route('/reset-password?token={token}&email={userEmail}',
		name: 'app_password_reset_page',
		requirements: ['token' => ".+"],
		defaults: ["userEmail" => "userEmail"])]
	public function modifyPasswordPage(
		Request                     $request,
		UserRepository              $userRepository,
		UserPasswordHasherInterface $userPasswordHasher,
		EntityManagerInterface      $entityManager,
		string                      $token,
		string                      $userEmail
	): Response
	{
		try {

			$user = $userRepository->findOneBy
			([
				"email" => $userEmail,
				"tokenValidator" => $token
			 ]);

			if (empty($user)) {

				$this->addFlash("error", "Une erreur est intervenue, veuillez réessayer");

				return $this->redirectToRoute("home");
			}

			$form = $this->createForm(ResetPasswordPageFormType::class, $user);

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {

				if($form->get('password')->getData() !== $form->get('confirmationPassword')->getData())
				{
					$this->addFlash("error", "La confirmation du mot de passe à échoué");

					return $this->redirectToRoute("home");
				}

				$user->setPassword(
					$userPasswordHasher->hashPassword(
						$user,
						$form->get('password')->getData()
					),
				);

				$entityManager->persist($user);
				$entityManager->flush();

				$this->addFlash("success", "Votre mot de passe à bien été mis à jour");

				return $this->redirectToRoute("home");
			}

			return $this->render("password/reset-password.html.twig", [
				"resetFormType" => $form->createView(),
			]);

		}catch (Exception $exception){

			$this->addFlash("error", "Une erreur est intervenue, veuillez réessayer");

			return $this->redirectToRoute("home");
		}
	}
}
