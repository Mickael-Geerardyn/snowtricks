<?php

namespace App\Service;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CustomTokenValidatorService extends AbstractController
{
	private CsrfTokenManagerInterface $tokenManager;
	public function __construct(CsrfTokenManagerInterface $tokenManager)
	{
		$this->tokenManager = $tokenManager;
	}

	/**
	 * @throws Exception
	 */
	public function validateCsrfToken(string $tokenId, string $token): bool|Response
    {
		try {

			$isValid = $this->tokenManager->isTokenValid(new CsrfToken($tokenId, $token));

			if(!$isValid)
			{
				return false;
			}

			return true;

		}catch (Exception $exception){

			$this->addFlash("error", "Une erreur est intervenue, veuillez réessayer");

			return $this->redirectToRoute("home");
		}
    }
}
