<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Utility\Upload;
use \Core\View;

/**
 * Product controller
 */
class Product extends \Core\Controller
{

    /**
     * Affiche la page d'ajout
     * @return void
     */
    public function indexAction()
    {
        if (isset($_POST['submit'])) {
            try {
                // Récupération des données
                $name = trim($_POST['name'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $user_id = $_SESSION['user']['id'] ?? null;
                $picture = $_FILES['picture'] ?? null;
    
                // Validation simple
                if (empty($name) || empty($description) || $picture['error'] === UPLOAD_ERR_NO_FILE) {
                    throw new \Exception("Tous les champs sont requis, y compris une image.");
                }
    
                // Sauvegarde de l'article
                $articleId = Articles::save([
                    'name' => $name,
                    'description' => $description,
                    'user_id' => $user_id
                ]);
    
                // Upload de l'image
                $pictureName = Upload::uploadFile($picture, $articleId);
                Articles::attachPicture($articleId, $pictureName);
    
                // Redirection
                header('Location: /product/' . $articleId);
                exit;
    
            } catch (\Exception $e) {
                // Affichage de l'erreur et rendu du formulaire avec message
                View::renderTemplate('Product/Add.html', [
                    'error' => $e->getMessage(),
                    'name' => $_POST['name'] ?? '',
                    'description' => $_POST['description'] ?? ''
                ]);
                return;
            }
        }
    
        // Premier affichage du formulaire (GET)
        View::renderTemplate('Product/Add.html');
    }
    

    /**
     * Affiche la page d'un produit
     * @return void
     */
    public function showAction()
    {
        $id = $this->route_params['id'];

        try {
            Articles::addOneView($id);
            $suggestions = Articles::getSuggest();
            $article = Articles::getOne($id);
        } catch(\Exception $e){
            var_dump($e);
        }

        View::renderTemplate('Product/Show.html', [
            'article' => $article[0],
            'suggestions' => $suggestions
        ]);
    }
}
