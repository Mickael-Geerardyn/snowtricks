<?php

namespace App\Service;

use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

class VideoRegex
{
	public function getVideoUrl(string $input): array
	{
			$srcPattern = '#(?<=src=").*?(?=")#';
			//$urlPattern = '#https:\/{2}.*#';

			preg_match($srcPattern, $input, $result);

			//if(!$result)
			//{
			//	preg_match($urlPattern, $input, $result);
			//}

			return $result;
	}
}