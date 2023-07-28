<?php

namespace App\Service;

class VideoRegex
{
	public function getVideoUrl(string $input): array
	{
		//(http[s]?:([/]{2}(\S+)[/embed](\S+)(?<!"))) Regex with /embed needed part
		$pattern = '#src\s*=\s*"(.+?)"#';

		preg_match($pattern, $input, $result);

		return $result;
	}
}