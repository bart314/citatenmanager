<?php
namespace App\Controller;

use App\Database\Quote;
use App\Database\Title;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/titel')]
class TitelController {
    #[Route('/all')]
    public function getAll():Response{
        return new JsonResponse([
            'type' => 'titel',
            'data' => Title::getAll()
        ]);
    }

    #[Route('/{id}')]
    public function getTitleById($id):Response {
        return new JsonResponse(Title::find($id));
    }

    #[Route('/{id}/all')]
    public function getAllFromTitle($id):Response {
        return new JsonResponse(Quote::find_by_title($id));
    }
}
