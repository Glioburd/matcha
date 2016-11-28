<?php

use \App\Middlewares\FlashMiddleware;
use \App\Middlewares\OldMiddleware;

require '../vendor/autoload.php';

session_start();

$app = new \Slim\App((['settings' => [
	'displayErrorDetails' => true
	]]));

require ('../app/container.php');

//Middlewares

$container = $app->getContainer();
$app->add(new FlashMiddleware($container->view->getEnvironment()));
$app->add(new OldMiddleware($container->view->getEnvironment()));
// $app->add(new App\Middlewares\TwigCsrfMiddleware($container->view->getEnvironment(), $container->csrf));
// $app->add($container->csrf);


$app->get('/', 'App\Controllers\PagesController:home')->setName('home');

$app->get('/contact', 'App\Controllers\PagesController:getContact')->setName('contact');
$app->post('/contact', 'App\Controllers\PagesController:postContact');

$app->get('/auth/signup', 'App\Controllers\PagesController:getSignUp')->setName('auth.signup');
$app->post('/auth/signup', 'App\Controllers\PagesController:postSignUp');

$app->get('/auth/signupinfos', 'App\Controllers\PagesController:getSignUpInfos')->setName('auth.signupinfos');
$app->post('/auth/signupinfos', 'App\Controllers\PagesController:postSignUpInfos');

$app->get('/auth/login', 'App\Controllers\PagesController:getLogIn')->setName('auth.login');
$app->post('/auth/login', 'App\Controllers\PagesController:postLogIn');

$app->get('/logout', 'App\Controllers\PagesController:getLogOut')->setName('logout');

$app->get('/profile/{userprofile}', 'App\Controllers\PagesController:getProfile')->setName('user.profile');
$app->post('/profile/{userprofile}', 'App\Controllers\PagesController:postProfile');

$app->get('/edit', 'App\Controllers\PagesController:getEdit')->setName('user.edit');
$app->post('/edit', 'App\Controllers\PagesController:postEdit');

$app->get('/settings', 'App\Controllers\PagesController:getSettings')->setName('user.settings');

$app->get('/uploadpic', 'App\Controllers\PagesController:getUploadPicture')->setName('upload.picture');
$app->post('/uploadpic', 'App\Controllers\PagesController:postUploadPicture');

$app->post('/change_avatar', 'App\Controllers\PagesController:postChangeAvatar')->setName('change.picture');

$app->post('/deletepic', 'App\Controllers\PagesController:postDeletePicture')->setName('delete.picture');

$app->post('/like', 'App\Controllers\PagesController:postLike')->setName('like');

$app->post('/unlike', 'App\Controllers\PagesController:postUnlike')->setName('unlike');

$app->post('/uploadpic2', 'App\Controllers\PagesController:postUploadPicture2')->setName('upload.picture2');;

$app->run();