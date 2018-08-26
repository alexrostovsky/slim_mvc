<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\App();


$app->get('/', function (Request $request, Response $response, array $args) {
    return $response->getBody()->write("Hello, MVC practice");
});


$app->get('/list', function (Request $request, Response $response, array $args) {
    $games = '[{"id":"1","name":"The Witcher 3: Wild Hunt","cost":"40","description":"The Witcher 3: Wild Hunt is a 2015 action role-playing video game developed and published by CD Projekt."}]';
    
    return $response->withJson([
        'games' => $games
    ], 200);
});

$app->get('/{id}', function (Request $request, Response $response, array $args) {
    $game = '{"id":"1","name":"The Witcher 3: Wild Hunt","cost":"40","description":"The Witcher 3: Wild Hunt is a 2015 action role-playing video game developed and published by CD Projekt."}';
    
    return $response->withJson([
        'game' => $game
    ], 200);
});

// require __DIR__ . '/../src/router.php';

$app->run();