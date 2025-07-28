<?php
/**
*        Class de la gestion des articles                            *
*        @author Ali MARZAK                                          *
*        @date 17/06/2025                                            *
**/


// Inclure le fichier de connexion PDO
require_once("models/connexion.php");
require_once("entities/article_entity.php");


class ArticleModel extends Connexion{
    
    public string   $strKeywords     = "";
    public int      $intPeriod       = 0;
    public string   $strDate         = "";
    public string   $strStartDate    = "";
    public string   $strEndDate      = "";
    public int      $intAuthor       = 0;
    

    
    /**
    *   Méthode permettant de récupérer les articles
    *   @param int $intLimit Nombre d'articles à récupérer
    **/
    public function fetchAllArticle(int $intLimit = 0){
        $params = [];
        $strAnd = " WHERE ";
        
        // $params['keywords'] = $_POST['keywords'];

        
        // Ecrire la requête comme dans PHPMyAdmin
        $strQuery       =   "SELECT article_id, article_title, article_img, article_content, article_createdate,
                                CONCAT(user_name, ' ', user_firstname) AS article_author, article_creator
                            FROM articles
                                INNER JOIN users ON article_creator = user_id ";
        if($this->strKeywords != ""){
            $params['keywords'] = "%" . $this->strKeywords . "%";
            $strQuery       .= $strAnd. " (article_title LIKE :keywords OR article_content LIKE :keywords) ";
            $strAnd = " AND ";
        }


        if($this->intAuthor > 0){
            $params['author'] = $this->intAuthor;
            $strQuery       .= $strAnd. "article_creator = :author ";
            $strAnd = " AND ";
        }

        if($this->intPeriod === 1 && isset($this->strStartDate) && isset($this->strEndDate)){
            $params['startDate'] = $this->strStartDate;
            $params['endDate'] = $this->strEndDate;
            $strQuery       .= $strAnd. " DATE_FORMAT(article_createdate, '%Y-%m-%d') > :startDate 
                                AND DATE_FORMAT(article_createdate, '%Y-%m-%d') < :endDate ";
        } elseif (!empty($this->strDate)){
            $params['date'] = $this->strDate;
            $strQuery       .= $strAnd. " DATE_FORMAT(article_createdate, '%Y-%m-%d') = :date ";
        }

        $strQuery       .= "ORDER BY article_createdate DESC ";
        if($intLimit > 0) {
            $strQuery .= "LIMIT 4 OFFSET 0";
        }

        // On execute la requête et on demande tous les résultats
        $stmt = $this->_db->prepare($strQuery);
        $stmt->execute($params);
        $arrArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $arrObjArticles = [];
        foreach($arrArticles as $arrDetArticle){
            $objArticle = new Article();
            $objArticle->hydrate($arrDetArticle);
            $arrObjArticles[] = $objArticle;
        }
        return $arrObjArticles;
    }

    // Récupère les articles en fonction de son identifiant
    public function fetchArticleById(int $intId){
        $strQuery   = "SELECT article_id, article_title, article_img, article_content, article_createdate, article_creator
                        FROM articles 
                        WHERE article_id = :id;";
        $strRqPrep  = $this->_db->prepare($strQuery);    
        $strRqPrep->bindValue(":id", $intId, PDO::PARAM_INT);
        $strRqPrep->execute();
        $arrArticle    = $strRqPrep->fetch();
        
        $objArticle = new Article();
        $objArticle->hydrate($arrArticle);
        
        return $objArticle;
    }

    public function createArticle(Article $article){
        // Ajouter les infos en BDD
        $strQuery    = "INSERT INTO articles (article_title, article_img, article_content, article_createdate, article_creator)
                        VALUES ('".$article->getTitle()."', '".$article->getImg()."', '".$article->getContent()."', NOW(), ".$_SESSION['id'].");";
                        
        //var_dump($strQuery);
        $this->_db->exec($strQuery);
    }

    public function updateArticle(Article $article){
        // Ajouter les infos en BDD
        $strQuery        = "UPDATE articles 
                            SET article_title = :title,
                                article_img = :img,
                                article_content = :content
                            WHERE article_id = :id; ";
        $strRqPrep    = $this->_db->prepare($strQuery);
        $strRqPrep->bindValue(":title", $article->getTitle(), PDO::PARAM_STR);
        $strRqPrep->bindValue(":img", $article->getImg(), PDO::PARAM_STR);
        $strRqPrep->bindValue(":content", $article->getContent(), PDO::PARAM_STR);
        $strRqPrep->bindValue(":id", $article->getId(), PDO::PARAM_INT);

        $strRqPrep->execute();
    }

    public function deleteArticle(int $intId){
        $strQuery   =   "DELETE FROM articles 
                        WHERE article_id = :id; ";
        $strRqPrep    = $this->_db->prepare($strQuery);  
        $strRqPrep->bindValue(":id", $intId, PDO::PARAM_INT);
        $strRqPrep->execute();

        return $strRqPrep->rowCount() > 0;
    }
}
    
    
    
