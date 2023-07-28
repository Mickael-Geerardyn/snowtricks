<?php

namespace App\Security;

use App\Controller\LoginController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Bundle\SecurityBundle\Security;

class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
	private Security $security;

    public function __construct(private UrlGeneratorInterface $urlGenerator, Security $security)
    {
		$this->security = $security;
	}

    public function authenticate(Request $request): Passport
    {
        $userName = $request->request->get('_username', '');

        $request->getSession()->set(Security::LAST_USERNAME, $userName);

        return new Passport(
            new UserBadge($userName),
            new PasswordCredentials($request->request->get('_password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {

            return new RedirectResponse($targetPath);
        }

		if (!$this->security->getUser()->IsVerified())
		{
			$session = new Session();
			$session->getFlashBag()->add("error", "Veuillez valider votre compte à l'aide du lien envoyé par email lors de l'inscription");

			return new RedirectResponse($this->urlGenerator->generate('home'));
		}

        // For example:
        return new RedirectResponse($this->urlGenerator->generate('home'));

        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
