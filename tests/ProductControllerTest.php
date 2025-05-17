<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\Product;

class ProductControllerTest extends TestCase
{
    protected function setUp(): void
    {
        // Simuler un utilisateur connecté
        $_SESSION['user'] = ['id' => 1];
        
        // Nettoyer les superglobales
        $_POST = [];
        $_FILES = [];
    }

    public function testSubmitWithoutPictureShowsErrorMessage()
    {
        // Simuler une soumission de formulaire sans image
        $_POST['submit'] = true;
        $_POST['name'] = 'Test';
        $_POST['description'] = 'Test';
        $_POST['price'] = 10;

        $_FILES['picture'] = [
            'name' => '',
            'full_path' => '',
            'type' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => 0
        ];

        // Capturer la sortie
        ob_start();
        $controller = new Product([]); // On passe un tableau vide pour éviter l'erreur
        $controller->indexAction();
        $output = ob_get_clean();

        // Vérifie qu'on a bien le message d'erreur dans la page rendue
        $this->assertStringContainsString('Tous les champs sont requis', $output);
    }
}
