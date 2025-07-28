<?php
    /**
    * Classe pour le contrôleur des pages
    *
    * Cette classe permet de s'occuper des pages.
    *
    * @category    Controller
    * @package     blog
    */
    class PageCtrl extends BaseCtrl{
        /**
        * Méthode qui affiche la page about
        **/
        public function about(){
            $this->render("about", [
                'strH1'                 => "A propos",
                'strPar'                => "Page de contenu",
                'strPage'               => "about"
            ]);
        }

        /**
        * Méthode qui affiche la page mentions
        **/
        public function mentions(){
            $this->render("mentions", [
                'strH1'                 => "Mentions légales",
                'strPar'                => "Page de contenu",
                'strPage'               => "mentions"
            ]);
        }

        /**
        * PMéthode qui affiche la page contact
        **/
        public function contact(){
            $this->render("contact", [
                'strH1'                 => "Contact",
                'strPar'                => "Page de contact",
                'strPage'               => "contact"
            ]);
        }
    }