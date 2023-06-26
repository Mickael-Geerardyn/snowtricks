<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;

class MailerController extends AbstractController
{
	private Security $security;
	private MailerInterface $mailer;

	public function __construct(Security $security, MailerInterface $mailer)
	{
		$this->security = $security;
		$this->mailer = $mailer;
	}

	/**
	 * @throws TransportExceptionInterface
	 */
	#[Route('/mailer', name: 'app_mailer')]
    public function sendMail(string $userEmail, string $userName): void
    {
		$email = (new TemplatedEmail())
			->to($userEmail)
			->subject('Validez votre inscription!')
			->htmlTemplate('mailer/index.html.twig')
			->context([
				'userName' => $userName,
					  ]);

		$this->mailer->send($email);
    }
}
