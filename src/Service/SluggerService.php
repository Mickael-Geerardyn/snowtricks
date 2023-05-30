<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
class SluggerService
{
	private SluggerInterface $slugger;
	public function __construct(private SluggerInterface $sluggerInterface)
	{
		$this->slugger = $sluggerInterface;
	}

	public function makeSlug(string $sluggerChain)
	{
		return $this->slugger->slug($sluggerChain);
	}
}