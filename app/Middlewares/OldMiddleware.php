<?php

namespace App\Middlewares;

/**
* 
*/
class OldMiddleware
{

	private $twig;

	public function __construct(\Twig_Environment $twig) {
		$this->twig = $twig;
	}

	public function __invoke($request, $response, $next){
		$this->twig->addGlobal('old', isset($_SESSION['old']) ? $_SESSION['old'] : []);
		if (isset($_SESSION['old'])) {
			unset($_SESSION['old']);
		}

		$response = $next($request, $response);
		if ($response->GetStatusCode() !== 200) {
			$_SESSION['old'] = $request->GetParams();
		}
		return $response;
	}

}