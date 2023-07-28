<?php

namespace App\Security;


use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CustomTokenGenerator
{
	private CsrfTokenManagerInterface $csrfTokenManager;

	public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
	{
		$this->csrfTokenManager = $csrfTokenManager;
	}

	public function getToken(string $tokenId): string
	{
			return $this->csrfTokenManager->getToken($tokenId)->getValue();
	}
}