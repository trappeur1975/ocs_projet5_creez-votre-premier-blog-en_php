<?php
namespace App\Entities;

/**
 * Form generate a forms
 */
class Form
{
    private $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }

    //pour creer un champ input
    public function input(string $key, string $label): string
    {
        $value = $this->getValue($key);
        $type = $key === "password" ? "password" : "text"; //CODE ORIGINAL GRAFIKART
        // -------------------- DEBUT DE MON CODE PERSO remplace code origien grafikart --------------------------
        // if($key === "password"){
        //     $type = "password";
        // } else if ($key === "number") {
        //     $type = "number";
        // } else if ($key === "email") {
        //     $type = "email";
        // } else if ($key === "url") {
        //     $type = "url";
        // }     
        // else {
        //     $type = "text";
        // }
        // -------------------- FIN DE MON CODE PERSO remplace code origien grafikart --------------------------
        
        return <<<HTML
        <div class="form-group">
            <label for="{$key}">{$label}</label>
            <input type={$type} id="{$key}" class="form-control" name="{$key}" value="{$value}">
        </div>
HTML;
    }

    //pour creer un champ textarea
    public function textarea(string $key, string $label): string
    {
        $value = $this->getValue($key);
        return <<<HTML
        <div class="form-group">
            <label for="{$key}">{$label}</label>
            <textarea type="text" id="{$key}" class="form-control" name="{$key}">{$value}</textarea>
        </div>
HTML;
    }

    //pour creer un champ select
    public function select(string $key, string $label, array $options=[]): string
    {
        $optionsHTML = [];
        foreach($options as $key =>$value){
            $optionsHTML[] = "<option value=\"$key\">$value</option>";
        }
        $value = $this->getValue($key);
        $optionsHTML = implode('', $optionsHTML);
        return <<<HTML
            <div class="form-group">
                <label for="field{$key}">{$label}</label>
                <select id="{$key}" class="form-control" name="{$key}">{$optionsHTML}</select>
            </div>
HTML;
    }

    
    // pour trouver le getter a executer pour avoir la valeur a integrer dans notre champs de notre formulaire
    // gerer a la fois si $key est un tableau ou un objet
    // private function getValue(string $key) :?string
    private function getValue(string $key)
    {
        // if(is_array($this->data)){
        //     return $this->data[$key] ?? null;
        // }
        $method = 'get'.ucfirst($key);
        // return $this->data->$method();
        $value = $this->data->$method();
        if($value instanceof \DateTimeInterface){
           return  $value->format('Y-m-d H:i:s');
        }
        return $value;
    }
}