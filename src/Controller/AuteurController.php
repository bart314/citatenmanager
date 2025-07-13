<?php

namespace App\Controller;

use App\Database\Auteur;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/auteur')]
class AuteurController {
    #[Route('/all')]
    public function getAllAuteurs(): Response {
        return new JsonResponse(Auteur::getAll());
    }

    #[Route('/search/{term}')]
    public function getCitatenByTerm($term):Response {
        return new JsonResponse(Quote::find_by_term($term));
    }

}