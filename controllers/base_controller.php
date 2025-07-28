<?php 
    /**
    * Classe pour la base du contrôleur
    *
    * Cette classe permet d'avoir une base contenant une fonction privée render.
    *
    * @category    Controller
    * @package     blog
    */
    class BaseCtrl {
        /**
        * Méthode render qui renvoie la vue des headers, footers et du contenu principal avec les variables.
        *
        * @param string $viewPath       Le chemin vers le fichier du contenu principal.
        * @param [] $variables          Tableau associatif dynamique contenant les variables.
        * @return void;
        */
        protected function render(string $viewPath, $variables = []): void {
            // Extraire les variables pour qu'elles soient accessibles dans la vue
            extract($variables);
            ob_start();
            include("views/_partial/header.php");
            include("views/{$viewPath}.php");
            include("views/_partial/footer.php");
        }
    }