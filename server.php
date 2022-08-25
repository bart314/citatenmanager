<?php
namespace App;
require 'db/Title.php';
require 'db/Quote.php';
require 'db/Collection.php';
require 'db/Auteur.php';

use App\Database\Quote;
use App\Database\Title;
use App\Database\Collection;
use App\Database\Auteur;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

/* TITELS */

$app->get('/titel/all', function(Request $request, Response $response) {
    $result = Title::getAll();
    $response->getBody()->write(json_encode(["type"=>"titel","data"=>$result], true));
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

$app->post('/titel/new', function(Request $request, Response $response) {
    $data = [
        "data" => $_POST,
        "quotes" => $_FILES['quotes']
    ];
    $result = Title::create($data);

    $response->getBody()->write(json_encode(['aantal_quotes'=>$result]));
    return $response;
});

/* AUTEURS */

$app->get('/auteur/all', function(Request $request, Response $response) {
    $result = Auteur::getAll();
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


/* COLLECTIONS */

$app->get('/collections/all', function(Request $request, Response $response) {
    $result = Collection::get_all();

    $response->getBody()->write(json_encode(["type"=>"collection","data"=>$result]));
    return $response;
});

$app->get('/collections/{id}/all', function(Request $request, Response $response, array $args) {
    $result = Collection::get($args['id']);

    $response->getBody()->write(json_encode($result));
    return $response;
});

$app->post('/collections/new', function(Request $request, Response $response) {
    $body = json_decode($request->getBody(), true);

    $coll_id = Collection::create($body);
    $response->getBody()->write(json_encode(["id" => (int)$coll_id]));
    return $response->withHeader("Status-Code", "201 Created");
});

$app->post('/collections/add', function(Request $request, Response $response) {
    $body = json_decode($request->getBody(), true);
    $tot = Collection::add_quotes($body['coll_id'], $body['quotes']);
    $response->getBody()->write(json_encode(['tot'=>$tot]));
    return $response;
});

/* APP SETTINGS AND START-UP */

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            // ->withHeader('Content-Type','application/json')
            ->withHeader('Content-Type','multipart/form-data');
});


$app->setBasePath("/server.php");
$app->run();


