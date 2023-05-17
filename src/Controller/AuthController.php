<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
	/**
	 * @return Response
	 */
    #[Route('/sign-up', name: 'sign_up_page')]
    public function signUpPage(Request $request): Response
    {
		dump($request);
        return $this->render('auth/sign-up.html.twig');
    }
}
