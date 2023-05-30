<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ConnectionFormType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{

	/**
	 * @param UserPasswordHasherInterface $passwordHasher
	 * @param Request                     $request
	 * @param EntityManagerInterface      $entityManager
	 *
	 * @return Response
	 */
    #[Route('/sign-in', name: 'sign_in_page', methods: ['GET','POST'])]
    public function signUpPage(UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
		try
		{
			$user = new User();

			$form = $this->createForm(ConnectionFormType::class, $user);
			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid())
			{
				$user = $userRepository->checkIfUserAlreadyExist($form->get('email')->getData());
				dd($user);
			}

			return $this->render('registration/sign-up.html.twig', [
				'registrationForm' => $form->createView(),
			]);

		} catch (Exception $exception)
		{
			return $this->render('home/homepage.html.twig', [
				"error" => $exception->getMessage()
			]);
		}

    }
}
