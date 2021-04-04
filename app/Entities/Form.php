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
    // PERO j ai rajouter l attribue "$title" pour nommer le champ differement du label
    public function input(string $key, string $label, string $title): string
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
        
        // modif perso j ai remplacé name="{$key}" par name="{$label}" ATTENTION VOIR L IMPLICATION QUE CELA A SUR LE SELECT MEDIA POUR L EDIT DE POST
        // modif perso j ai remplacé for="{$key}" par for="{$label}"
        return <<<HTML
        <div class="form-group">
            <label for="{$label}">{$title}</label>
            <input type={$type} id="{$label}" class="form-control" name="{$label}" value="{$value}">
            <!-- <input type={$type} id="{$label}" class="form-control" name="{$label}" value="{$value}"> -->
        </div>
HTML;
    }

    //pour creer un champ textarea
    public function textarea(string $key, string $label): string
    {
        $value = $this->getValue($key);
        return <<<HTML
        <div class="form-group">
            <label for="{$label}">{$label}</label>
            <!-- <label for="{$key}">{$label}</label> -->
            <textarea type="text" id="{$label}" class="form-control" name="{$key}">{$value}</textarea>
            <!-- <textarea type="text" id="{$key}" class="form-control" name="{$key}">{$value}</textarea> -->
        </div>
HTML;
    }

    //pour creer un champ select simple
    //perso j ai rajouter string $title pour que l affichage du champs en front soit different du label (avant il n'y avait que label)
    public function selectSimple(string $key, string $label, string $title, array $options=[]): ?string
    { 
        if ($this->data != []) { //pour gerer le cas ou il n'a aucune donnée transmise (exemple pour un post qu il n'ait aucun media lier a celui ci)
            $optionsHTML = [];
            $value = $this->getValue($key);
            
            foreach($options as $mykey => $myValue){
                $selected = "";
                if ($value == $mykey){
                    $selected= " selected";
                }

                $optionsHTML[] = "<option value=\"$mykey\"$selected>$myValue</option>";
            } 
            
            $optionsHTML = implode('', $optionsHTML);

            return <<<HTML
                <div class="form-group">
                    <label for="field{$label}">{$title}</label>
                    <!-- <label for="field{$key}">{$title}</label> -->
                    <select id="{$label}" class="form-control" name="{$label}">{$optionsHTML}</select>
                    <!-- <select id="{$key}" class="form-control" name="{$label}">{$optionsHTML}</select> -->
                </div>
HTML;
        } else {
            return null;
        }

    }

    //pour creer un champ select multiple
    //perso j ai rajouter array $selects pour que l'on puisse recuperer la liste (ex des medias propre a un post) pour ensuite gerer les attributs
    //perso j ai rajouter string $title pour que l affichage du champs en front soit different du label (avant il n'y avait que label)
    public function selectMultiple(string $key, string $label, string $title, array $options=[], array $selects): ?string
    { 
        if ($this->data != []) { //pour gerer le cas ou il n'a aucune donnée transmise (exemple pour un post qu il n'ait aucun media lier a celui ci)
            $optionsHTML = [];
            $value = $this->getValue($key);
    
            foreach($options as $mykey => $myValue){
                $selected = "";
                foreach($selects as $select){
                    if ($select == $mykey){
                        $selected= " selected";
                    }
                }
                $optionsHTML[] = "<option value=\"$mykey\"$selected>$myValue</option>";
            }
            
            $optionsHTML = implode('', $optionsHTML);

            return <<<HTML
                <div class="form-group">
                    <!-- <label for="field{$key}">{$label}</label> -->
                    <label for="field{$label}">{$title}</label>
                    
                    <!-- ATTENTION rajout de "[]" a "name="{$label}" pour pouvoir recupérer les donnees des select multiple DE CE FAIT ON RECUPERE UN TABLEAU DE DONNEE ET NON UNE SEUL DONNEE -->
                    <select id="{$label}" class="form-control" name="{$label}[]" multiple>{$optionsHTML}</select>
                    <!-- <select id="{$key}" class="form-control" name="{$label}[]" multiple>{$optionsHTML}</select> -->
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