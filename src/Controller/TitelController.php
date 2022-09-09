<?php
namespace App\Controller;

use App\Database\Quote;
use App\Database\Title;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/titel')]
class TitelController {
    #[Route('/all', methods:['GET'])]
    public function getAll():Response{
        return new JsonResponse([
            'type' => 'titel',
            'data' => Title::getAll()
        ]);
    }

    #[Route('/{id}', methods:['GET'])]
    public function getTitleById($id):Response {
        return new JsonResponse(Title::find($id));
    }

    #[Route('/{id}/all', methods:['GET'])]
    public function getAllFromTitle($id):Response {
        return new JsonResponse(Quote::find_by_title($id));
    }

    #[Route('/new', methods:['POST'])]
    public function newTitle(Request $request):Response {
//        return new JsonResponse($_FILES);
        $params = [
            "titel" => $_POST['titel'],
            "jaartal" => $_POST['jaartal'],
            "auteur_id" => $_POST['auteur_id'] ?? 0,
            "voornaam" => $_POST['voornaam'],
            "achternaam" => $_POST['achternaam'],
            "quotes" => $_FILES['quotes'] ?? 0
        ];
        $rv = Title::create($params);
        return new JsonResponse($rv, 201);
//        return new JsonResponse(Title::create($params));
    }
}
