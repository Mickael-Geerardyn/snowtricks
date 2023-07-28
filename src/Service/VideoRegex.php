<?php

namespace App\Service;

class VideoRegex
{
	public function getVideoUrl(string $input): array
	{
		$pattern = '#(?<=src=").*?(?=")"#';

		preg_match($pattern, $input, $result);

		return $result;
	}
}