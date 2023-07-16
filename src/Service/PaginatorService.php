<?php

namespace App\Service;

use App\Entity\Figure;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Request;

class PaginatorService
{
	private MessageRepository $messageRepository;
	private array $commentsQueryPagesKeys = ["commentsPage", "next", "previous"];

	public function __construct(MessageRepository $messageRepository)
	{
		$this->messageRepository = $messageRepository;
	}

	public function getCurrentPageComments(Figure $figure, int|null $page): object
	{

		if($page)
		{
			$comments = $this->messageRepository->getPaginatedComments($figure, $page);

		}else{

			$comments = $this->messageRepository->getPaginatedComments($figure);
		}


		return $comments;
	}

	public function getTotalPagesPerFigure(Figure $figure): int
	{
		$totalPages = count($figure->getMessages()) / 10;

		if(is_float($totalPages))
		{
			$totalPages = ceil($totalPages);
		}

		return intval($totalPages);
	}

	public function getPreviousPage(int $currentPage, int $totalPages): int
	{
		if($currentPage === 1)
		{
			return $currentPage = $totalPages;
		}

		return intval($currentPage - 1);
	}

	public function getNextPage(int $currentPage): int
	{
		return intval($currentPage + 1);
	}

	public function getParameterKey(Request $request): string|bool
	{
		foreach($this->commentsQueryPagesKeys as $parameterKey)
		{
			if($request->get($parameterKey))
			{
				return $parameterKey;
			}
		}
		return false;
	}

	public function getCurrentPage(Request $request, int $totalPages): ?int
	{
		$parameter = $this->getParameterKey($request);

		return match ($parameter) {
			"commentsPage" => intval($request->get($parameter)),
			"previous" => $this->getPreviousPage(intval($request->get($parameter)), $totalPages),
			"next" => $this->getNextPage(intval($request->get($parameter))),
			default => null,
		};
	}

	public function getFinalPage(Request $request, int $totalPages): ?int
	{
		$currentPage = $this->getCurrentPage($request, $totalPages);

		if($currentPage < 1 || $currentPage > $totalPages)
		{
			$currentPage = 1;
		}

		return $currentPage;
	}
}