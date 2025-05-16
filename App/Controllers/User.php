<?php

namespace App\Controllers;

use App\Config;
use App\Model\UserRegister;
use App\Models\Articles;
use App\Utility\Hash;
use App\Utility\Session;
use \Core\View;
use Exception;
use http\Env\Request;
use http\Exception\InvalidArgumentException;

/**
 * User controller
 */
class User extends \Core\Controller
{

    /**
     * Affiche la page de login
     */
    public function loginAction()
    {
        if(isset($_POST['submit'])){
            $f = $_POST;

            // TODO: Validation

            $this->login($f);

            // Si login OK, redirige vers le compte
            header('Location: /account');
        }

        View::renderTemplate('User/login.html');
    }

    /**
     * Page de création de compte
     */
    public function registerAction()
    {
        if(isset($_POST['submit'])){
            $f = $_POST;

            if($f['password'] !== $f['password-check']){
                // TODO: Gestion d'erreur côté utilisateur
            }

            // validation

            $this->register($f);
            // TODO: Rappeler la fonction de login pour connecter l'utilisateur
        }

        View::renderTemplate('User/register.html');
    }

    /**
     * Affiche la page du compte
     */
    public function accountAction()
    {
        $articles = Articles::getByUser($_SESSION['user']['id']);

        View::renderTemplate('User/account.html', [
            'articles' => $articles
        ]);
    }

    /*
     * Fonction privée pour enregister un utilisateur
     */
    private function register($data)
    {
        try {
            // Generate a salt, which will be applied to the during the password
            // hashing process.
            $salt = Hash::generateSalt(32);

            $userID = \App\Models\User::createUser([
                "email" => $data['email'],
                "username" => $data['username'],
                "password" => Hash::generate($data['password'], $salt),
                "salt" => $salt
            ]);

            return $userID;

        } catch (Exception $ex) {
            // TODO : Set flash if error : utiliser la fonction en dessous
            /* Utility\Flash::danger($ex->getMessage());*/
        }
    }

    private function login($data)
    {
        try {
            if (!isset($data['email'])) {
                throw new Exception('Email manquant');
            }
    
            $user = \App\Models\User::getByLogin($data['email']);
    
            if (!$user) {
                throw new Exception("Utilisateur introuvable");
            }
    
            if (Hash::generate($data['password'], $user['salt']) !== $user['password']) {
                throw new Exception("Mot de passe incorrect");
            }
    
            // Authentification réussie : on sauvegarde en session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
            ];
    
            // Se souvenir de moi ?
            if (!empty($data['remember_me'])) {
                $token = bin2hex(random_bytes(32)); // 64 caractères sécurisés
                $expires = time() + (86400 * 30); // 30 jours
    
                // Stockage en base (via un champ `remember_token` et `remember_token_expires`)
                \App\Models\User::storeRememberToken($user['id'], $token, $expires);
    
                // Création du cookie sécurisé
                setcookie('remember_me', $token, [
                    'expires' => $expires,
                    'path' => '/',
                    'httponly' => true,
                    'secure' => isset($_SERVER['HTTPS']),
                    'samesite' => 'Strict',
                ]);
            }
    
            return true;
    
        } catch (Exception $ex) {
            // TODO: Utiliser Flash pour afficher les erreurs
            // \Utility\Flash::danger($ex->getMessage());
            return false;
        }
    }
    


    /**
     * Logout: Delete cookie and session. Returns true if everything is okay,
     * otherwise turns false.
     * @access public
     * @return boolean
     * @since 1.0.2
     */
    public function logoutAction() {
    // 1. Supprime le token de la BDD
    if (isset($_SESSION['user']['id'])) {
        \App\Models\User::clearRememberToken($_SESSION['user']['id']);
    }

    // 2. Supprime le cookie remember_me
    if (isset($_COOKIE['remember_me'])) {
        setcookie('remember_me', '', time() - 3600, "/");
        unset($_COOKIE['remember_me']);
    }

    // 3. Supprime la session
    session_unset();
    session_destroy();

    header('Location: /login');
    exit;
    }

}
