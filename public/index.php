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

/* Ajax */

$app->get('/LOL', 'App\Controllers\ChatController:getLOL');

$app->post('/notifreadtamere', 'App\Controllers\PagesController:postNotifsRead')->setName('notifsread');
$app->post('/notifcountunread', 'App\Controllers\PagesController:postCountNotifsUnread')->setName('countNotifsUnread');

/* Home */
$app->get('/', 'App\Controllers\PagesController:home')->setName('home');


/* Contact Page */
$app->get('/contact', 'App\Controllers\PagesController:getContact')->setName('contact');
$app->post('/contact', 'App\Controllers\PagesController:postContact');


/* Register */
$app->get('/auth/signup', 'App\Controllers\PagesController:getSignUp')->setName('auth.signup');
$app->post('/auth/signup', 'App\Controllers\PagesController:postSignUp');


/* Register second part */
$app->get('/auth/signupinfos', 'App\Controllers\PagesController:getSignUpInfos')->setName('auth.signupinfos');
$app->post('/auth/signupinfos', 'App\Controllers\PagesController:postSignUpInfos');


/* Login */
$app->get('/auth/login', 'App\Controllers\PagesController:getLogIn')->setName('auth.login');
$app->post('/auth/login', 'App\Controllers\PagesController:postLogIn');


/* Logout */
$app->get('/logout', 'App\Controllers\PagesController:getLogOut')->setName('logout');



/* Profile */
$app->get('/profile/{userprofile}', 'App\Controllers\PagesController:getProfile')->setName('user.profile');
$app->post('/profile/{userprofile}', 'App\Controllers\PagesController:postProfile');

$app->post('/like', 'App\Controllers\PagesController:postLike')->setName('like');

$app->post('/unlike', 'App\Controllers\PagesController:postUnlike')->setName('unlike');

$app->post('/block', 'App\Controllers\PagesController:postBlockUser')->setName('block');

$app->post('/unblock', 'App\Controllers\PagesController:postUnblockUser')->setName('unblock');

/* Settings */
$app->get('/settings', 'App\Controllers\PagesController:getSettings')->setName('user.settings');
$app->post('/settings', 'App\Controllers\PagesController:postSettings');

$app->get('/settings/changeMail', 'App\Controllers\PagesController:getChangeMail');

$app->post('/settings/unblock', 'App\Controllers\PagesController:postUnblockUserFromSettings')->setName('unblockFromSettings');


/* Edit profile */
$app->get('/edit', 'App\Controllers\PagesController:getEdit')->setName('user.edit');
$app->post('/edit', 'App\Controllers\PagesController:postEdit');

$app->post('/uploadpic', 'App\Controllers\PagesController:postUploadPicture')->setName('upload.picture');

$app->post('/change_avatar', 'App\Controllers\PagesController:postChangeAvatar')->setName('change.picture');

$app->post('/deletepic', 'App\Controllers\PagesController:postDeletePicture')->setName('delete.picture');

$app->run();