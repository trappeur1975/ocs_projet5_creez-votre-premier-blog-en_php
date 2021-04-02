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
    //perso j ai rajouter array $select pour que l'on puisse recuperer la liste (ex des medias propre a un post) pour ensuite gerer l attribut select (ou les attributs dans le cas de select multiple)
    //perso j ai rajouter $typeSelect=null pour gerer si c'est un select multiple
    //perso j ai rajouter string $title pour que l affichage du champs en front soit different du label (avant il n'y avait que label)
    public function select(string $key, string $label, string $title, array $options=[], array $selects=null, $typeSelect=null): ?string
    { 
        if ($this->data != []) { //pour gerer le cas ou il n'a aucune donnée transmise (exemple pour un post qu il n'ait aucun media lier a celui ci)
            $optionsHTML = [];
            $value = $this->getValue($key);

            if ($typeSelect == null){ //cas pour select "simple"
                foreach($options as $mykey => $myValue){
                    $selected = "";
                    if ($value == $mykey){
                        $selected= " selected";
                    }
    
                    $optionsHTML[] = "<option value=\"$mykey\"$selected>$myValue</option>";
                }
            } else {//cas pour select "multiple"     
                foreach($options as $mykey => $myValue){
                    $selected = "";
                    foreach($selects as $select){
                        if ($select == $mykey){
                            $selected= " selected";
                        }
                    }
                    $optionsHTML[] = "<option value=\"$mykey\"$selected>$myValue</option>";
                }
            }
            
            $optionsHTML = implode('', $optionsHTML);

            return <<<HTML
                <div class="form-group">
                    <!-- <label for="field{$key}">{$label}</label> -->
                    <label for="field{$key}">{$title}</label>
                    
                    <!-- ATTENTION rajout de "[]" a "name="{$label}" pour pouvoir recupérer les donnees des select multiple
                    DE CE FAIT ON RECUPERE UN TABLEAU DE DONNEE ET NON UNE SEUL DONNEE => FAIRE ATTENTION POUR LES SELECT SIMPLE (comme pour le select des user dans l'edit d'un post) -->
                    <select id="{$key}" class="form-control" name="{$label}[]" $typeSelect>{$optionsHTML}</select>
                    <!-- <select id="{$key}" class="form-control" name="{$label}" $typeSelect>{$optionsHTML}</select> -->
                </div>
HTML;
        } else {
            return null;
        }

    }

    
    // pour trouver le getter a executer pour avoir la valeur a integrer dans notre champs de notre formulaire
    // gerer a la fois si $key est un tableau ou un objet
    // private function getValue(string $key) :?string
    private function getValue(string $key): ?string //voir si on garde ce typage de retour car peut poser probleme avec un SELECT
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