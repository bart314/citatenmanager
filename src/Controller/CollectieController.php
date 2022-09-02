<?php

namespace App\Controller;

use App\Database\Collection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/collections')]
class CollectieController {
    #[Route('/all')]
    public function getAllCollections():Response {
        return new JsonResponse([
            "type" => "collection",
            "data" => Collection::get_all()
        ]);
    }

    #[Route('/{id}/all')]
    public function getCollectionById($id):Response {
        return new JsonResponse(Collection::get($id));
    }

    #[Route('/new', methods:['POST'])]
    public function createNewCollection(Request $request):Response {
        // gewoon $_POST gebruiken, want binnen een http context werkt
        // de `getContent()` functie blijkbaar niet
        // https://stackoverflow.com/a/46562823/10974490

        $params = [
            "naam" => $_POST['naam'],
            "quotes" => $_POST['quotes'] ?? ''
        ];
        $id = Collection::create($params);
        return new JsonResponse(["id" => $id], 201);
    }

    #[Route('/add', methods:['POST'])]
    public function addToCollection(Request $request):Response {
        $params = json_decode($request->getContent());
        $tot = Collection::add_quotes($params->coll_id, $params->quotes);
        return new JsonResponse(["tot" => $tot]);
    }


}