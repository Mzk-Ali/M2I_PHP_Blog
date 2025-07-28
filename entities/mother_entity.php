<?php

class Entity{
    protected string $_prefixe;
    private int $_intId;


    /**
    * Fonction qui permet d'hydrater l'objet de manière automatique
    **/
    public function hydrate(Array $arrUsers){
        foreach($arrUsers as $key => $value){
            $newKey = ucfirst(str_replace($this->_prefixe .'_', '', $key));
            $method = 'set' . $newKey;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function getId(): int{
        return $this->_intId;
    }

    public function setId(int $intId){
        $this->_intId = $intId;
    }
}