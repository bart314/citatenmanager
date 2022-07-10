<?php
namespace App;
require 'db/Title.php';
require 'db/Quote.php';

use App\Database\Quote;
use App\Database\Title;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

/* TITELS */

$app->get('/titel/all', function(Request $request, Response $response) {
    $result = Title::getAll();
    $response->getBody()->write(json_encode($result));
    return $response;
});

$app->get('/titel/{id}',  function(Request $request, Response $response, array $args) {
    $result = Title::find($args['id']);
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/titel/{id}/all',  function(Request $request, Response $response, array $args) {
    $result = Quote::find_by_title($args['id']);
    $response->getBody()->write(json_encode($result));
    return $response;
});

/* CITATEN */

$app->get('/citaat/{id}',  function(Request $request, Response $response, array $args) {
    $result = Quote::read($args['id']);
    $response->getBody()->write(json_encode($result));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/citaat/search/{term}',  function(Request $request, Response $response, array $args) {
    $result = Quote::find_by_term($args['term']);
    $response->getBody()->write(json_encode($result));
    return $response;
});

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Content-Type','application/json');
});

$app->setBasePath("/server.php");
$app->run();


