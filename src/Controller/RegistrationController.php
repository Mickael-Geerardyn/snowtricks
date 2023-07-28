<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\CustomTokenGenerator;
use App\Service\DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
	/**
	 * @param Request                     $request
	 * @param Session                     $session
	 * @param UserPasswordHasherInterface $userPasswordHasher
	 * @param EntityManagerInterface      $entityManager
	 * @param MailerController            $mailerController
	 * @param DateTime                    $dateTime
	 * @param CustomTokenGenerator        $customTokenGenerator
	 *
	 * @return Response
	 */
    #[Route('/sign-up', name: 'sign_up_page', methods: ['GET','POST'])]
    public function register(
        Request $request,
        Session $session,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        MailerController $mailerController,
        DateTime $dateTime,
		CustomTokenGenerator $customTokenGenerator
    ): Response
    {
		try
		{
			$user = new User();
			$form = $this->createForm(RegistrationFormType::class, $user);

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid())
			{
				$user->setPassword(
					$userPasswordHasher->hashPassword(
						$user,
						$form->get('plainPassword')->getData()
					),
				);

				$user->setCreatedAt();

				$validatorToken = $customTokenGenerator->getToken($user->getEmail());

				$user->setTokenValidator($validatorToken);

				$entityManager->persist($user);
				$entityManager->flush();

				$mailerController->sendRegistrationMail($user->getEmail(), $user->getName(), $validatorToken);

				return $this->render('home/homepage.html.twig', ['success' => "Validez votre inscription à l'aide du courriel envoyé à l'adresse renseignée!"]);
			};

			return $this->render("registration/sign-up.html.twig", [
				"registrationForm" => $form->createView(),
			]);

		} catch(Exception|TransportExceptionInterface $exception)
		{
			return $this->render("home/homepage.html.twig", [

				"error" => $exception->getMessage()
			]);
		}
    }

    #[Route("/registration-confirmation?token={token}&email={userEmail}",
		name: "app_registration_validation",
		requirements: ['token' => ".+"],
		defaults: ["userEmail" => "userEmail"] ,
		methods: ['GET']
	)]
    public function registrationValidation(string $token, string $userEmail, UserRepository $userRepository): Response
	{
		$user = $userRepository->findOneBy([
												"email" => $userEmail,
											   "tokenValidator" => $token
										   ]);

		if (empty($user)) {
			return $this->render("home/homepage.html.twig", [
				"error" => "Une erreur est survenue, veuillez réessayer"
			]);
		}

		$userRepository->userAccountValidation($user);

		return $this->render("home/homepage.html.twig", [
			"success" => "Votre compte est activé!"
		]);

	}
}
