<?php

use App\Database\Quote;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/citaat')]
class CitaatController {
    #[Route('/{id}')]
    public function getCitaatById($id): Response {
        return new JsonResponse(Quote::read($id));
    }

    #[Route('/search/{term}')]
    public function getCitatenByTerm($term):Response {
        return new JsonResponse(Quote::find_by_term($term));
    }

}
