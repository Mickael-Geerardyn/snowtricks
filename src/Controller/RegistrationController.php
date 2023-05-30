<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
	/**
	 * @param Request                     $request
	 * @param UserPasswordHasherInterface $userPasswordHasher
	 * @param EntityManagerInterface      $entityManager
	 *
	 * @return Response
	 */
    #[Route('/sign-up', name: 'sign_up_page', methods: ['GET','POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
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

				$this->redirectToRoute('home');
			};

			return $this->render("registration/sign-up.html.twig", [
				"registrationForm" => $form->createView(),
			]);

		} catch(Exception $exception)
		{
			return $this->render("home/homepage.html.twig", [

				"error" => $exception->getMessage()
			]);
		}
    }
}
