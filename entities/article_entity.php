<?php

require_once("entities/mother_entity.php");

class Article extends Entity{
    private string $_strTitle = "";
    private string $_strContent = "";
    private string $_strImg = "";
    private string $_datCreateDate;
    private int $_intCreator;
    private string $_intAuthor;
    // private ?User $_objAuthor = null;

    public function __construct(){
        $this->_prefixe = "article";
    }

    public function getTitle(): string {
        return $this->_strTitle;
    }
    
    public function setTitle(string $strTitle){
        // Enlever les espaces avant et après => trim()
        $strTitle        = str_replace("<script>", "", $strTitle);
        $strTitle        = str_replace("</script>", "", $strTitle);
        $strTitle        = htmlspecialchars(trim($strTitle));
        $this->_strTitle = $strTitle;
    }
    
    public function getContent(): string {
        return $this->_strContent;
    }
    
    public function getSummary(int  $intNbCar = 50){
        $strSummary    = substr($this->_strContent, 0, $intNbCar).'...';
        return $strSummary;
    }
    
    public function setContent(string $strContent){
        $strContent        = htmlspecialchars(trim($strContent));
        $this->_strContent = $strContent;
    }
    
    public function getImg(): string {
        return $this->_strImg;
    }

    public function setImg(string $strImg){
        $this->_strImg = $strImg;
    }

    public function setInitImg(string $strImg){
        
        // Récupérer l'extension du fichier
        $arrFileName     = explode(".", $strImg);
        // L'extension est le dernier élément du tableau
        /*$strExtension    = $arrFileName[count($arrFileName)-1];*/
        $strExtension    = end($arrFileName);

        // Génération d'un nom de fichier avec date + aléatoire
        $objDate         = new DateTimeImmutable();
        $strImageName    = $objDate->format('YmdHis').bin2hex(random_bytes(5)).".".$strExtension;
        $this->_strImg = $strImageName;
    }
    
    public function getCreateDate(): string {
        return $_datCreateDate;
    }
    
    /**
    * Getter qui permet de formatter la date de création
    * @param string $strFormat le format de sortie, par défaut 'd/m/Y'
    **/
    public function getDateFormat(string $strFormat = 'd/m/Y'): string{
        $objDate     = new DateTimeImmutable($this->_datCreateDate);
        $strDate     = $objDate->format($strFormat);
        return $strDate;
    }
    
    public function setCreateDate(string $createDate){
        $this->_datCreateDate = $createDate;
    }
    
    public function getCreator(): int {
        return $this->_intCreator;
    }
    
    public function setCreator(int $intCreator){
        $this->_intCreator = $intCreator;
    }
    
    public function getAuthor(): string {
        return $this->_intAuthor;
    }
    
    public function setAuthor(string $intAuthor){
        $this->_intAuthor = $intAuthor;
    }
    
    // public function getAuthor(): User {
        // return $this->_objAuthor;
    // }
    
    // public function setAuthor(User $author){
        // $this->_objAuthor = $author;
    // }
}