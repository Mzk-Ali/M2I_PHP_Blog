<?php
/**
 * Nom du fichier : index.php
 * Description    : Fichier principale du projet.
 * 
 * @author        Ali MARZAK
 * @version       1.0.0
 * @package       blog
 * @license       
 * @since         2025-06-20
 */
    require_once("controllers/base_controller.php");

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $allowedControllers = ['article', 'user', 'page', 'error'];

    // Récupération des paramètres dans l'url
    $strController = isset($_GET['ctrl']) ? strtolower($_GET['ctrl']) : 'article';
    $strAction     = isset($_GET['action']) ? strtolower($_GET['action']) : 'home';

    // Vérifie si le controller est permis
    if (!in_array($strController, $allowedControllers)) {
        // http_response_code(403);
        // exit("Controller invalide.");
        header("Location:index.php?ctrl=error&action=error_403.php");
    }

    // Construction du chemin du controller
    $file = __DIR__ . "/controllers/{$strController}_controller.php";

    // Vérifie si le fichier existe
    if(file_exists($file)) {
        require_once($file);
    } else {
        // http_response_code(404);
        // exit("Fichier controller introuvable.");
        header("Location:index.php?ctrl=error&action=error_404.php");
    }

    $strCtrlName = ucfirst($strController)."Ctrl";

    // Vérifie si la classe existe
    if(!class_exists($strCtrlName)) {
        // http_response_code(404);
        // exit("Classe introuvable.");
        header("Location:index.php?ctrl=error&action=error_404.php");
    }

    $objCtrl = new $strCtrlName();

    // Vérifie si la méthode existe
    if (!method_exists($objCtrl, $strAction)) {
        // http_response_code(404);
        // exit("Méthode introuvable.");
        header("Location:index.php?ctrl=error&action=error_404.php");
    }

    $objCtrl->$strAction();

?>