<?php

namespace App\Middlewares;
use Slim\Csrf\Guard;


/**
* 
*/
class TwigCsrfMiddleware
{

	private $twig;

	public function __construct(\Twig_Environment $twig, Guard $csrf) {
		$this->twig = $twig;
		$this->csrf = $csrf;
	}

	public function __invoke($request, $response, $next){

		$csrf = $this->csrf;

		$this->twig->addFunction(new \Twig_SimpleFunction('csrf', function() use ($csrf, $request){
			$nameKey = $csrf->getTokenNameKey();
			$valueKey = $csrf->getTokenValueKey();
			$name = $request->getAttribute($nameKey);
			$value = $request->getAttribute($valueKey);
			return "
			<input type=\"text\" name=\"$nameKey\" value=\"$name\">
			<input type=\"text\" name=\"$valueKey\" value=\"$value\">
			";
		}, ['is_safe' => ['html']]));
		return $next($request, $response);
	}

}