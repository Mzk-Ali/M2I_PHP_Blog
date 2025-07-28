<?php
    /**
    * Classe pour le contrôleur des erreurs
    *
    * Cette classe permet de s'occuper des erreurs.
    *
    * @category    Controller
    * @package     blog
    */
    class ErrorCtrl extends BaseCtrl{
        /**
        * Méthode qui affiche la page d'erreur 403
        **/
        public function error_403(){
            $this->render("error/error_403", [
                'strH1'                 => "Vous n'êtes pas autorisé",
                'strPar'                => "Page d'erreur",
                'strPage'               => "error_403"
            ]);
        }

        /**
        * Méthode qui affiche la page d'erreur 404
        **/
        public function error_404(){
            $this->render("error/error_404", [
                'strH1'                 => "Page introuvable",
                'strPar'                => "Page d'erreur",
                'strPage'               => "error_404"
            ]);
        }
    }