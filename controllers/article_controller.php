<?php

    require_once("models/article_model.php");
    require_once("models/user_model.php");


    /**
    * Classe pour le contrôleur des articles
    *
    * Cette classe permet de s'occuper des fonctionnalités pour les articles.
    *
    * @category    Controller
    * @package     blog
    */
    class ArticleCtrl extends BaseCtrl{
        private ArticleModel            $_obj_articleModel;
        private UserModel               $_obj_userModel;
        private Article                 $_obj_article;

        public function __construct(){
            $this->_obj_articleModel    = new ArticleModel();
            $this->_obj_userModel       = new UserModel();
            $this->_obj_article         = new Article();
        }


        /**
        * Page d'accueil
        **/
        public function home(){
            $arrArticles        = $this->_obj_articleModel->fetchAllArticle(4);

            $this->render("home", [
                'strH1'         => "Accueil",
                'strPar'        => "Page affichant les 4 derniers articles",
                'strPage'       => "home",
                'arrArticles'   => $arrArticles
            ]);
        }

        /**
        * Page Blog
        **/
        public function blog(){
            $strKeywords    = $_POST['keywords']??"";
            $intPeriod        = $_POST['period']??0;
            $strDate        = $_POST['date']??"";
            $strStartDate    = $_POST['startdate']??"";
            $strEndDate        = $_POST['enddate']??"";
            $intAuthor        = $_POST['author']??0;


            // Donner à la classe ArticleModel les infos de recherche
            $this->_obj_articleModel->strKeywords    = $strKeywords;
            $this->_obj_articleModel->intPeriod      = $intPeriod;
            $this->_obj_articleModel->strDate        = $strDate;
            $this->_obj_articleModel->strStartDate   = $strStartDate;
            $this->_obj_articleModel->strEndDate     = $strEndDate;
            $this->_obj_articleModel->intAuthor      = $intAuthor;

            $arrArticles = $this->_obj_articleModel->fetchAllArticle();

            $arrUsers = $this->_obj_userModel->fetchAllUser();

            $this->render("blog", [
                'strH1'         => "Blog",
                'strPar'        => "Page affichant tous les articles, avec une zone de recherche sur les articles",
                'strPage'       => "blog",
                'arrArticles'   => $arrArticles,
                'strKeywords'   => $strKeywords,
                'intPeriod'     => $intPeriod,
                'strDate'       => $strDate,
                'strStartDate'  => $strStartDate,
                'strEndDate'    => $strEndDate,
                'intAuthor'     => $intAuthor
            ]);
        }

        /**
        * Page Ajout d'article
        **/
        public function add_article(){
            // Vérifier que l'utilisateur est connecté
            if (!isset($_SESSION['id']) || $_SESSION['id'] == '') {
                // var_dump($_SESSION['id']);die;
                // Si l'utilisateur n'est pas connecté => page 403
                header("Location:index.php?ctrl=error&action=error_403.php");
            }

            $this->_obj_article->hydrate($_POST);

            // Tableau des types MIME autorisés
            $arrMimeTypes     = array('image/jpeg', 'image/png');

            $arrImg             = $_FILES['img']??array();

            // initialise le tableau des erreurs
            $arrErrors    = array(); 
            // Si le formulaire a été envoyé
            if (count($_POST) > 0){
                // Si l'utilisateur n'a pas saisi le titre
                if ($this->_obj_article->getTitle() == ""){
                    $arrErrors['title'] = "Le titre est obligatoire";
                }
                // Si l'utilisateur n'a pas choisi d'image
                if ($arrImg['error'] == 4){
                    $arrErrors['img'] = "L'image est obligatoire";
                /*}else if ( 
                    ($arrImg['type'] != 'image/jpeg') && ($arrImg['type'] != 'image/png')
                ){*/
                }else if (!in_array($arrImg['type'], $arrMimeTypes)){
                    $arrErrors['img'] = "L'image n'est pas au bon format";
                }else if ($arrImg['error'] > 0){
                    $arrErrors['img'] = "Il y a une erreur sur le fichier image";
                }

                // Si l'utilisateur n'a pas saisi de contenu
                if ($this->_obj_article->getContent() == ""){
                    $arrErrors['content'] = "Le contenu est obligatoire";
                }

                // Si le formulaires est OK
                if (count($arrErrors) == 0){
                    $this->_obj_article->setInitImg($arrImg['name']);

                    // Copier l'image
                    if (!move_uploaded_file($arrImg['tmp_name'], 'assets/images/'.$this->_obj_article->getImg())){
                        $arrErrors['img'] = "Il y a une erreur sur le fichier image";
                    }else{
                        $this->_obj_articleModel->createArticle($this->_obj_article);
                        $_SESSION['message'] = "L'article a bien été ajouté";
                        header("Location:index.php");
                    }
                }
            }

            $this->render("article/add_article", [
                'strH1'         => "Ajouter un article",
                'strPar'        => "Page permettant d'ajouter un article",
                'strPage'       => "add_article",
                'newArticle'    => $this->_obj_article,
                'arrErrors'     => $arrErrors
            ]);
        }


        /**
        * Page de modification d'article
        **/
        public function edit_article(){
            // Vérifier que l'utilisateur est connecté
            if (!isset($_SESSION['id']) || $_SESSION['id'] == '') {
                // Si l'utilisateur n'est pas connecté => page 403
                header("Location:index.php?ctrl=error&action=error_403.php");
            }

            if(!isset($_GET['articleId'])){
                header("Location:index.php?ctrl=error&action=error_403.php");
            }

            $article = $this->_obj_articleModel->fetchArticleById($_GET['articleId']);
            // Tableau des types MIME autorisés
            $arrMimeTypes     = array('image/jpeg', 'image/png');

            $arrImg             = $_FILES['img']??array();

            // initialise le tableau des erreurs
            $arrErrors    = array(); 
            // Si le formulaire a été envoyé
            if (count($_POST) > 0){
                $article->hydrate($_POST);
                // Si l'utilisateur n'a pas saisi le titre
                if ($article->getTitle() == ""){
                    $arrErrors['title'] = "Le titre est obligatoire";
                }
                // Si l'utilisateur n'a pas choisi d'image
                if ($arrImg['error'] == 4){
                    $arrErrors['img'] = "L'image est obligatoire";
                /*}else if ( 
                    ($arrImg['type'] != 'image/jpeg') && ($arrImg['type'] != 'image/png')
                ){*/
                }else if (!in_array($arrImg['type'], $arrMimeTypes)){
                    $arrErrors['img'] = "L'image n'est pas au bon format";
                }else if ($arrImg['error'] > 0){
                    $arrErrors['img'] = "Il y a une erreur sur le fichier image";
                }

                // Si l'utilisateur n'a pas saisi de contenu
                if ($article->getContent() == ""){
                    $arrErrors['content'] = "Le contenu est obligatoire";
                }

                // Si le formulaires est OK
                if (count($arrErrors) == 0){
                    $oldNameImg = $article->getImg();
                    $article->setInitImg($arrImg['name']);
                    // Copier l'image
                    if (!move_uploaded_file($arrImg['tmp_name'], 'assets/images/'.$article->getImg())){
                        $arrErrors['img'] = "Il y a une erreur sur le fichier image";
                    }else{
                        $this->_obj_articleModel->updateArticle($article);
                        if(file_exists('assets/images/'.$oldNameImg)){
                            unlink('assets/images/'.$oldNameImg);
                        }
                        $_SESSION['message'] = "L'article a bien été modifié";
                        header("Location:index.php");
                    }
                }
            }

            $this->render("article/edit_article", [
                'strH1'         => "Modifier mon article",
                'strPar'        => "Page permettant de modifier mon article",
                'strPage'       => "edit_article",
                'article'       => $article,
                'arrErrors'     => $arrErrors
            ]);
        }


        /**
        * Page de suppression d'article
        **/
        public function delete_article(){
            // Vérifier que l'utilisateur est connecté
            if (!isset($_SESSION['id']) || $_SESSION['id'] == '') {
                // Si l'utilisateur n'est pas connecté => page 403
                header("Location:index.php?ctrl=error&action=error_403.php");
            }

            if(!isset($_GET['articleId'])){
                header("Location:index.php?ctrl=error&action=error_403.php");
                exit;
            }

            $articleId = (int) $_GET['articleId'];

            $article = $this->_obj_articleModel->fetchArticleById($articleId);

            $successDelete = $this->_obj_articleModel->deleteArticle($articleId);

            if($successDelete){
                $imagePath = 'assets/images/' . $article->getImg();
                if(file_exists($imagePath)){
                    unlink($imagePath);
                }
                $_SESSION['message'] = "L'article a bien été supprimé.";
            } else{
                $_SESSION['message'] = "L'article n'a pas été supprimé";
            }

            header("Location:index.php");
            exit;
        }
    }