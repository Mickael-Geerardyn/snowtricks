<?php

namespace App\Security;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomTokenGenerator extends AbstractController
{
	public function getToken()
	{
		try {

			$tokenProvider = $this->container->get("security.csrf.token_manager");

			return $tokenProvider->getToken("validator_token")->getValue();

		}catch (NotFoundExceptionInterface|ContainerExceptionInterface $exception) {

			return $this->render("home/homepage.html.twig", [
				"error" => "Une erreur est intervenue, veuillez réessayer. Si le problème persiste, veuillez prendre contact avec l'administrateur du site.",
			]);
		}

	}
}