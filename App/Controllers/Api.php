<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Models\Cities;
use Core\View;
use Exception;
use OpenApi\Attributes as OA;

#[OA\Info(title: "API Vide Grenier", version: "1.0")]
#[OA\Server(url: "http://localhost:8080")]
class Api extends \Core\Controller
{
    #[OA\Get(
        path: "/api/products",
        summary: "Liste les produits",
        parameters: [
            new OA\Parameter(
                name: "sort",
                in: "query",
                required: false,
                description: "Tri des articles",
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des articles"
            )
        ]
    )]
    public function ProductsAction()
    {
        $query = $_GET['sort'] ?? null;
        $articles = Articles::getAll($query);

        header('Content-Type: application/json');
        echo json_encode($articles);
    }

    #[OA\Get(
        path: "/api/cities",
        summary: "Recherche de villes",
        parameters: [
            new OA\Parameter(
                name: "query",
                in: "query",
                required: true,
                description: "Nom de la ville à rechercher",
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des villes correspondantes"
            )
        ]
    )]
    public function CitiesAction()
    {
        $cities = Cities::search($_GET['query']);

        header('Content-Type: application/json');
        echo json_encode($cities);
    }
}
