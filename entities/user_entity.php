<?php

require_once("entities/mother_entity.php");

class User extends Entity {
    private string $_strName            = "";
    private string $_strFirstname       = "";
    private string $_strMail            = "";
    private string $_strPwd             = "";
    private string $_strCode            = "";

    public function __construct(){
        $this->_prefixe = "user";
    }

    public function getName(): string {
        return $this->_strName;
    }

    public function setName(string $strName){
        // Enlever les espaces avant et après => trim()
        $this->_strName = trim($strName);
    }

    public function getFirstname(): string {
        return $this->_strFirstname;
    }

    public function setFirstname(string $strFirstname){
        // Enlever les espaces avant et après => trim()
        $this->_strFirstname = trim($strFirstname);
    }

    public function getMail(): string {
        return $this->_strMail;
    }

    public function setMail(string $strMail){
        // Enlever les espaces avant et après => trim()
        $this->_strMail = strtolower(trim($strMail));
    }
    
    public function getPwd(): string {
        return $this->_strPwd;
    }

    public function setPwd(string $strPwd){
        $this->_strPwd = $strPwd;
    }

    public function getCode(): string {
        return $this->_strCode;
    }

    public function setCode(string $strCode){
        $this->_strCode = $strCode;
    }

    public function verifyPwd(string $strPwd): bool {
        return password_verify($strPwd, $this->_strPwd);
    }

    public function getPwdHash(): string {
        return password_hash($this->_strPwd, PASSWORD_DEFAULT);
    }

    // public function setHashPwd(string $strPwd){
        // Hacher le mot de passe
        // $strPwdHash = password_hash($strPwd, PASSWORD_DEFAULT);
        // $this->_strPwd = $strPwdHash;
    // }

    public function __toString(){
        return $this->_strName ." ". $this->_strFirstname;
    }

}