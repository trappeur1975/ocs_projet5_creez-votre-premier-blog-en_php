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

    public function input(string $key, string $label): string
    {
        $value = $this->getValue($key);
        return <<<HTML
        <div class="form-group">
            <label for="{$key}">{$label}</label>
            <input type="text" id="{$key}" class="form-control" name="{$key}" value="{$value}">
        </div>
HTML;
    }

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
        if($value instanceof \DateTimeInterface){ //
           return  $value->format('Y-m-d H:i:s');
        }
        return $value;
    }
}