# Matcha

## What's that?

This project is the second web project of 42 school. The purpose of Matcha is to discover the basics of a framework (ie routing system), by making a basic dating site.
I did it with PHP with the micro-framework Slim3, and using MySQL. And bootstrap 3 for the CSS.

## Restrictions:

* Only micro-frameworks are allowed (exemple for PHP : Slim3, for Python : Flask, for Ruby : Sinatra, for Node : Express, etc...)
* No ORM allowed ("hand written" SQL queries)
* No validator (ie checking myself the data which are submitted)
* Some other minors instructions: Footer, header, responsive, displayable on mobile, etc...

## Main parts

The developpement of this project is kind of cut in 5 parts:

* Registration and connexion : the user can register the basics informations (name, nickname, mail, age...) and will have to provide more specifics informations (hobbies, gender, sexual orientation...). The user has to be geolocated. If he refuses, he will still be localized with his/her IP. 
* User profile : each users must have a profile, with a popularity score, and can edit his/her own. It must be public, and an user can "like" or block an other user. Also, an user can check who visited his/her profile, and who liked him/her.
* Gettings matches : when an user is "ready" to get marched with people, a list of user he/she should be interested with will be displayed. The matches are calculated from the distance, the common hobbies and their respective sexual orientations. The list can be filtered with some options such as popularity, age, maximum distance, etc. People being blocked won't be displayed in the matches.
* Chat : if two users like each other, they can start a chat. At the beginning I wanted to use websockets, with Ratchet, but I didn't really have the time for it. So, the chat is a basic old chat system, by refreshing every x seconds the chat div with an ajax request.
* Notifications : an user will get a notification when he'll be visited by someone, or if someone like/unlike him/her, or when someone sends him/her a chat message.

## Getting Started

Simply clone the project, preferably name it matcha for the email features (reporting an account).

## Prerequisites

PHP 7 and MySQL.

## Installing

Go to matcha/ and run

```
php composer.phar install
```

or

```
composer install
```

if you have composer already installed.


To create the database, run in your browser

```
http://localhost:8080/config/setup.php
```

OR run in your shell from matcha/

```
sh config/config.sh
```

The second way will create the database AND will import users. Their profile pictures are initially stored in the premade_users folder.

## Note

Matcha was my first web project using a framework. It took me ~5 weeks. It wasn't really hard, but pretty long.
I definitely should have splitted my page controller to multiples specifics controllers. Which could have improved the readability of the controller.

The API used for the geolocation is the Google Maps API. The API used for getting the location from the IP is ipinfo (http://ipinfo.io).
