<?php

namespace App\Service;

class FilesService
{
	public function getOriginalFileName(object $file): array|string
	{
		return pathinfo($file->getClientOriginalName(),PATHINFO_FILENAME);
	}

	public function getNewFileName(string $safeFilename, object $file): string
	{
		return $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
	}
}