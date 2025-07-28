<?php

    require_once("models/user_model.php");
    require_once("entities/user_entity.php");
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    /**
    * Classe pour le contrôleur des users
    *
    * Cette classe permet de s'occuper des fonctionnalités pour les utilisateurs.
    *
    * @category    Controller
    * @package     blog
    */
    class UserCtrl extends BaseCtrl{
        private UserModel               $_obj_userModel;
        private User                    $_obj_user;

        public function __construct(){
            $this->_obj_userModel       = new UserModel();
            $this->_obj_user            = new User();
        }

        /**
        * Page de creation de compte
        **/
        public function create_account(){
            $this->_obj_user->hydrate($_POST);
            $strConfirm_pwd             = $_POST['confirm_pwd']??"";

            // initialise le tableau des erreurs
            $arrErrors                  = array(); 
            // Si le formulaire a été envoyé
            if (count($_POST) > 0){
                // Si l'utilisateur n'a pas saisi son nom
                if ($this->_obj_user->getName() == ""){
                    $arrErrors['name']  = "Le nom est obligatoire";
                }
                // Si l'utilisateur n'a pas saisi son prénom
                if ($this->_obj_user->getFirstname() == ""){
                    $arrErrors['firstname'] = "Le prénom est obligatoire";
                }
                // Si l'utilisateur n'a pas saisi son mail
                if ($this->_obj_user->getMail() == ""){
                    $arrErrors['mail']  = "Le mail est obligatoire";
                }elseif(!filter_var($this->_obj_user->getMail(), FILTER_VALIDATE_EMAIL)){
                    $arrErrors['mail']  = "Le mail n'est pas valide";
                }else{
                    $user               = $this->_obj_userModel->fetchUserByMail($this->_obj_user->getMail());
                    // Si j'ai un résultat => erreur
                    if(!$user){
                        $arrErrors['mail'] = "Le mail est déjà utilisé";
                    }
                }

                // Si l'utilisateur n'a pas saisi son mot de passe
                if ($this->_obj_user->getPwd() == ""){
                    $arrErrors['pwd']   = "Le mot de passe est obligatoire";
                }elseif ($this->_obj_user->getPwd() != $strConfirm_pwd){
                    $arrErrors['confirm_pwd'] = "Le mot de passe et sa confirmation ne correspondent pas";
                }

                // $newUser->setHashPwd($newUser->getPwd());

                // Si le formulaires est OK
                if (count($arrErrors) == 0){
                    $this->_obj_userModel->createUser($this->_obj_user);

                    $_SESSION['message']= "Vous compte à bien été créé, vous pouvez vous connecter";
                    // Redirection vers la page d'accueil
                    header("Location:index.php?ctrl=user&action=login.php");
                }
            }

            $this->render("user/create_account", [
                'strH1'                 => "Créer un compte",
                'strPar'                => "Page permettant de créer son compte",
                'strPage'               => "create_account",
                'newUser'               => $this->_obj_user,
                'arrErrors'             => $arrErrors
            ]);
        }

        /**
        * Page de connexion
        **/
        public function login(){
            $strMail                    = $_POST['mail']??"";
            $strPwd                     = $_POST['pwd']??"";
            
            // Enlever les espaces avant et après => trim()
            $strMail                    = strtolower(trim($strMail));

            // initialise le tableau des erreurs
            $arrErrors                  = array(); 
            // Si le formulaire a été envoyé
            if (count($_POST) > 0){
                // Si l'utilisateur n'a pas saisi son mail
                if ($strMail == ""){
                    $arrErrors['mail']  = "Le mail est obligatoire";
                }

                // Si l'utilisateur n'a pas saisi son mot de passe
                if ($strPwd == ""){
                    $arrErrors['pwd']   = "Le mot de passe est obligatoire";
                }

                // Si le formulaires est OK
                if (count($arrErrors) == 0){
                    // Vérifier les infos en BDD
                    $this->_obj_user    = $this->_obj_userModel->fetchUserByMail($strMail, false);

                    // Si aucun utilisateur trouvé
                    if(!$this->_obj_user){
                        $arrErrors[]    = "Erreur dans la connexion";
                    }else{
                        // On vérifie le mot de passe
                        if (password_verify($strPwd, $this->_obj_user->getPwd())) {
                            // unset($arrUser['user_pwd']);
                            $this->_obj_user->setPwd("");
                            // Ajouter les informations de l'utilisateur => en session
                            $_SESSION['prenom']             = $this->_obj_user->getFirstname();
                            $_SESSION['id']                 = $this->_obj_user->getId(); // La clé peut être renommée
                            $_SESSION['nom']                = $this->_obj_user->getName();
                            $_SESSION['message']            = "Vous êtes bien connecté";
                            // Redirection vers la page d'accueil
                            header("Location:index.php");
                        }else{
                            $arrErrors[]                    = "Erreur dans la connexion";
                        }
                    }
                }
            }

            $this->render("user/login", [
                'strH1'                 => "Se connecter",
                'strPar'                => "Page permettant de se connecter",
                'strPage'               => "login",
                'strMail'               => $strMail,
                'arrErrors'             => $arrErrors
            ]);
        }

        /**
        * Page de déconnexion
        **/
        public function logout(){
            // Initialisation de la session (car pas de header inclus) 
            session_start();
            // Destruction de la session
            session_destroy();
            // Redirection vers la page d'accueil
            header("Location:index.php");
        }

        /**
        * Page de modification de compte
        **/
        public function edit_account(){
            // Vérifier que l'utilisateur est connecté
            if (!isset($_SESSION['id']) || $_SESSION['id'] == '') {
                // Si l'utilisateur n'est pas connecté => page 403
                header("Location:index.php?ctrl=error&action=error_403.php");
            }

            // Récupérer les informations de l'utilisateur connecté
            $intID                      = $_SESSION['id'];

            $user = $this->_obj_userModel->fetchUserById($intID);

            $strMail                    = $user->getMail();

            $strPwd                     = $_POST['pwd']??"";
            $strConfirm_pwd             = $_POST['confirm_pwd']??"";

            
            // initialise le tableau des erreurs
            $arrErrors                  = array(); 
            // Si le formulaire a été envoyé
            if (count($_POST) > 0){
                $user->hydrate($_POST);
                // Si l'utilisateur n'a pas saisi son nom
                if ($user->getName() == ""){
                    $arrErrors['name']  = "Le nom est obligatoire";
                }
                // Si l'utilisateur n'a pas saisi son prénom
                if ($user->getFirstname() == ""){
                    $arrErrors['firstname'] = "Le prénom est obligatoire";
                }
                // Si l'utilisateur n'a pas saisi son mail
                if ($user->getMail() == ""){
                    $arrErrors['mail']  = "Le mail est obligatoire";
                }elseif(!filter_var($user->getMail(), FILTER_VALIDATE_EMAIL)){
                    $arrErrors['mail']  = "Le mail n'est pas valide";
                }elseif($user->getMail() != $strMail){
                    // Si l'adresse mail a été changée, vérifier son unicité en bdd
                    $userExist          = $this->_obj_userModel->fetchUserByMail($user->getMail());
                    // Si j'ai un résultat => erreur
                    if($userExist){
                        $arrErrors['mail'] = "Le mail est déjà utilisé";
                    }
                }
                
                // Si l'utilisateur veut modifier son mot de passe
                if ( ($strPwd != "") && ($strPwd != $strConfirm_pwd) ){
                    $arrErrors['confirm_pwd'] = "Le mot de passe et sa confirmation ne correspondent pas";
                }
                
                // Si le formulaires est OK
                if (count($arrErrors) == 0){
                    $this->_obj_userModel->updateUser($user);
                    
                    $_SESSION['prenom']                     = $user->getFirstname();
                    $_SESSION['nom']                        = $user->getName();
                    $_SESSION['message']                    = "Vos informations ont bien été modifiées";
                    // Redirection vers la page d'accueil
                    header("Location:index.php");
                }
            }

            // Fonction mère render pour l'affichage
            $this->render("user/edit_account", [
                'strH1'                 => "Modifier un compte",
                'strPar'                => "Page permettant de modifier un compte",
                'strPage'               => "edit_account",
                'user'                  => $user,
                'arrErrors'             => $arrErrors
            ]);
        }

        /**
        * Page d'initialisation de mot de passe
        **/
        public function recover_pwd(){
            $strCode                    = $_GET['code']??'';
            $strPwd                     = $_POST['pwd']??"";
            $strConfirm_pwd             = $_POST['confirm_pwd']??"";

            // Récupère les utilisateurs en fonction du code
            $objUser                    = $this->_obj_userModel->fetchUserByCode($strCode);

            // initialise le tableau des erreurs
            $arrErrors                  = array(); 
            if(!$objUser){
                $arrErrors[]            = "Le lien de récupération n'est plus valide";
            }else{
                // Si le formulaire a été envoyé
                if (count($_POST) > 0){
                    // Si l'utilisateur n'a pas saisi son mot de passe
                    if ($strPwd == ""){
                        $arrErrors['pwd'] = "Le mot de passe est obligatoire";
                    }elseif ($strPwd != $strConfirm_pwd){
                        $arrErrors['confirm_pwd'] = "Le mot de passe et sa confirmation ne correspondent pas";
                    }
                    
                    // Si le formulaires est OK
                    if (count($arrErrors) == 0){
                        $objUser->hydrate($_POST);
                        $this->_obj_userModel->updateUser($objUser);

                        $_SESSION['message']= "Votre mot de passe a bien été modifié, vous pouvez vous connecter";
                        // Redirection vers la page de connexion
                        header("Location:index.php?ctrl=user&action=login.php");
                    }    
                }
            }

            $this->render("user/recover_pwd", [
                'strH1'                 => "Réinitialiser son mot de passe",
                'strPar'                => "Page permettant de réinitaliser son mot de passe",
                'strPage'               => "recover_pwd",
                'arrErrors'             => $arrErrors
            ]);
        }

        /**
        * Page de mot de passe oublié
        **/
        public function forgot_pwd(){
            require'libs/PHPMailer/Exception.php';
            require'libs/PHPMailer/PHPMailer.php';
            require'libs/PHPMailer/SMTP.php';

            $strMail                    = $_POST['mail']??"";
            
            // initialise le tableau des erreurs
            $arrErrors                  = array(); 
            // Si le formulaire a été envoyé
            if (count($_POST) > 0){
                // Si l'utilisateur n'a pas saisi son mail
                if ($strMail == ""){
                    $arrErrors['mail']  = "Le mail est obligatoire";
                }elseif(!filter_var($strMail, FILTER_VALIDATE_EMAIL)){
                    $arrErrors['mail']  = "Le mail n'est pas valide";
                }else{
                    // Mail ok
                    // Récupère les utilisateurs qui ont l'adresse Mail
                    $this->_obj_user    = $this->_obj_userModel->fetchUserByMail($strMail, false);

                    // Si j'ai un résultat => envoyer un mail 
                    if($this->_obj_user){
                        $this->_obj_user->setCode(bin2hex(random_bytes(20)));
                        // Mise à jour de l'utilisateur (code + date/heure de demande de réinitialisation)
                        $this->_obj_userModel->updateUser($this->_obj_user);
                        // Envoyer le mail
                        $mail = new PHPMailer();
                        $mail->IsSMTP();
                        $mail->Mailer         = "smtp";
                        $mail->SMTPDebug    = 0;
                        $mail->SMTPAuth        = TRUE;
                        $mail->SMTPAutoTLS    = false; // A désactiver uniquement en local
                        $mail->SMTPSecure    = "tls";
                        $mail->Port         = 587;
                        $mail->Host         = "smtp.gmail.com";
                        $mail->Username        = '';
                        $mail->Password        = '';                
                        $mail->IsHTML(true);
                        $mail->CharSet        = "utf-8";
                        $mail->setFrom('no-reply@gmail.com', 'christel'); // Gmail ne change pas l'adresse
                        $mail->addAddress($strMail, $this->_obj_user->getName());
                        $mail->Subject        = 'BLOG - Réinitialisation du mot de passe';
                        
                        $strLien            = "recover_pwd.php?code=".$this->_obj_user->getCode();
                        
                        $mail->Body         = "<p>Bonjour ".$this->_obj_user->getName().",</p>
                                                <p>Vous avez demandé la réinitialisation du mot de passe</p>
                                                <p>Vous pouvez cliquer sur ce lien <a href='".$strLien."'>".$strLien."</a></p>
                                                <p>Ce lien sera valable 15 minutes</p>
                                                <p>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer ce mail</p>
                                                <p>L'équipe du BLOG</p>
                                                ";
                        if (!$mail->send()) {
                            $arrErrors[] = "Le message n'a pas pu être envoyé, merci de contacter l'administrateur";
                        }
                    }
                    if (count($arrErrors) == 0){
                        $_SESSION['message'] = "Votre demande de réinitialisation a été traitée, 
                                                si vous êtes inscrit vous allez recevoir un mail avec un lien";    
                        header("Location:index.php?ctrl=user&action=login.php");
                        die;
                    }
                }
            }

            $this->render("user/forgot_pwd", [
                'strH1'                 => "Mot de passe oublié",
                'strPar'                => "Page de saisie du mail pour réinitialiser le mot de passe",
                'strPage'               => "forgot_pwd",
                'strMail'               => $strMail,
                'arrErrors'             => $arrErrors
            ]);
        }

    }