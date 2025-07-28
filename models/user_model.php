<?php
/**
*        Class de la gestion des users                               *
*        @author Ali MARZAK                                          *
*        @date 17/06/2025                                            *
**/


// Inclure le fichier de connexion PDO
require_once("models/connexion.php");
require_once("entities/user_entity.php");


class UserModel extends Connexion{
    
    public function fetchAllUser(){
        $strQuery   =   "SELECT user_id, user_name, user_firstname, user_mail, user_pwd
                        FROM users";
                        
        // On execute la requête et on demande tous les résultats
        $arrUsers    = $this->_db->query($strQuery)->fetchAll();

        $arrObjUsers = [];
        foreach($arrUsers as $arrDetUser){
            $objUser = new User();
            $objUser->hydrate($arrDetUser);
            $arrObjUsers[] = $objUser;
        }
        
        return $arrObjUsers;
    }


    public function fetchUserByMail(string $strMail, bool $boolMail = true):array|Object{
        $strQuery    = "SELECT user_mail";
        if(!$boolMail){
            $strQuery    .= ", user_firstname, user_name, user_pwd, user_id";
        }
        $strQuery    .= " FROM users 
                        WHERE user_mail = :mail;";
        $strRqPrep  = $this->_db->prepare($strQuery);    
        $strRqPrep->bindValue(":mail", $strMail, PDO::PARAM_STR);
        $strRqPrep->execute();
        $arrUser    = $strRqPrep->fetch();

        $objUser = new User();
        $objUser->hydrate($arrUser);

        return $objUser;
    }


    // Récupère les utilisateurs en fonction de son identifiant
    public function fetchUserById(int $intId){
        $strQuery   = "SELECT user_id, user_name, user_firstname, user_mail
                        FROM users 
                        WHERE user_id = :id;";
        $strRqPrep  = $this->_db->prepare($strQuery);    
        $strRqPrep->bindValue(":id", $intId, PDO::PARAM_INT);
        $strRqPrep->execute();
        $arrUser    = $strRqPrep->fetch();
        
        $objUser = new User();
        $objUser->hydrate($arrUser);
        
        return $objUser;
    }

    public function createUser(User $user){
        // Ajouter les infos en BDD
        $strQuery        = "INSERT INTO users 
                            (user_name, user_firstname, user_mail, user_pwd)
                            VALUES 
                            ('".$user->getName()."', '".$user->getFirstname()."', '".$user->getMail()."', '".$user->getPwdHash()."');";
        $this->_db->exec($strQuery);
    }

    public function updateUser(User $user){
        // Modifier les infos en BDD
        $strQuery        = "UPDATE users 
                            SET user_name = :name,
                                user_firstname = :firstname,
                                user_mail = :mail,
                                user_code = :code";
        if ($user->getPwd() != ""){
            $strQuery    .=    " , user_pwd = :pwd ";
        }
        $strQuery    .=    " WHERE user_id = :id; ";
        $strRqPrep    = $this->_db->prepare($strQuery);    
        $strRqPrep->bindValue(":name", $user->getName(), PDO::PARAM_STR);
        $strRqPrep->bindValue(":firstname", $user->getFirstname(), PDO::PARAM_STR);
        $strRqPrep->bindValue(":mail", $user->getMail(), PDO::PARAM_STR);
        $strRqPrep->bindValue(":code", $user->getCode(), PDO::PARAM_STR);
        $strRqPrep->bindValue(":id", $user->getId(), PDO::PARAM_INT);
        
        if ($user->getPwd() != ""){
            $strRqPrep->bindValue(":pwd", $user->getPwdHash(), PDO::PARAM_STR);
        }

        $strRqPrep->execute();
    }

    public function fetchUserByCode(string $strCode){
        // Récupère les utilisateurs en fonction du code
        $strQuery                       =  "SELECT user_mail, user_id, user_name
                                            FROM users 
                                            WHERE user_code = :code
                                                AND DATE_ADD(user_code_date, INTERVAL 15 MINUTE) > NOW();";
        $strRqPrep                      = $this->_db->prepare($strQuery);    
        $strRqPrep->bindValue(":code", $strCode, PDO::PARAM_STR);
        $strRqPrep->execute();
        $arrUser                        = $strRqPrep->fetch();

        $objUser = new User();
        $objUser->hydrate($arrUser);

        return $objUser;
    }
}