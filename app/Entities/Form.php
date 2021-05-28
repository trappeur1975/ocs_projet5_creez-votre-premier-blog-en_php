<?php
namespace App\Entities;

/**
 * Form generate a forms
 */
class Form
{
    /**
     * edit
     *
     * @var boolean $edit to check if the creation of the form will be done in the case of a creation or an edition (of post for example).
     * by default this variable is false via the constructor because we are not in the case of creating a form for an edit 
     */
    private $edit;
    
    public function __construct($data, $edit = false)
    {
        $this->data = $data;
        $this->edit = $edit;
    }

   
    /**
     * Method input to create an input field 
     *
     * @param string $key
     * @param string $label
     * @param string $title
     * @return string
     */
    public function input(string $key, string $label, string $title): string
    {
        $value = $this->getValue($key);
        $type = $key === "password" ? "password" : "text";
        
        return <<<HTML
        <div class="form-group">
            <label for="{$label}">{$title}</label>
            <input type={$type} id="{$label}" class="form-control" name="{$label}" value="{$value}">
        </div>
HTML;
    }
  
    /**
     * Method inputFile to create an input field of type file 
     *
     * @param string $id
     * @param string $label
     * @param string $title
     * @return string
     */
    public function inputFile(string $id, string $label, string $title): string
    {
        return <<<HTML
        <div class="form-group">
            <label for="{$label}">{$title}</label>
            <input type=file id="{$id}" class="form-control" name="{$label}">
        </div>
HTML;
    }

    /**
     * Method inputImage to create an input field of type image 
     *
     * @param Media $logo
     * @param string $alt
     * @param string $label
     * @param string $title
     * @return string
     */
    public function inputImage(Media $logo, string $alt, string $label, string $title): string
    {
        $path = substr($logo->getPath(),1); //force to use substr to remove the dot in front of the logo path (=> ./media / ...) 

        return <<<HTML
        <div class="form-group">
            <label for="{$label}">{$title}</label>
            <input type=image id="{$label}" alt="{$alt}" class="form-control" name="{$label}" src="{$path}">
        </div>
HTML;
    }

    /**
     * Method textarea to create a textarea field 
     *
     * @param string $key
     * @param string $label
     * @return string
     */
    public function textarea(string $key, string $label): string
    {
        $value = $this->getValue($key);
        return <<<HTML
        <div class="form-group">
            <label for="{$label}">{$label}</label>
            <textarea type="text" id="{$label}" class="form-control" name="{$key}">{$value}</textarea>
        </div>
HTML;
    }
  
    /**
     * Method selectSimple to create a simple select field 
     *
     * @param string $key
     * @param string $label
     * @param string $title
     * @param array $options
     * @return string
     */
    public function selectSimple(string $key, string $label, string $title, array $options=[]): ?string
    { 
        if ($this->data != []) { // to manage the case where it has no transmitted data (example for a post that it has no media linked to this one) 
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
                    <select id="{$label}" class="form-control" name="{$label}">{$optionsHTML}</select>
                </div>
HTML;
        } else {
            return null;
        }

    }
  
    /**
     * Method selectMultiple to create a multiple select field 
     *
     * @param string $key
     * @param string $label so that the display of the front field is different from the label (before there was only label)
     * @param string $title
     * @param array $options
     * @param array $selects so that we can retrieve the list (eg media specific to a post) to then manage the attributes 
     * @return string
     */
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
                    <label for="field{$label}">{$title}</label>
                    <select id="{$label}" class="form-control" name="{$label}[]" multiple>{$optionsHTML}</select>
                </div>
HTML;
        } else {
            return null;
        }

    }

    /**
     * Method getValue to find the getter to execute to have the value to integrate in our field of our form 
     *
     * @param string $key handle both if $ key is an array or an object 
     *
     * @return string
     */
    private function getValue(string $key): ?string // see if we keep this return typing because it can pose a problem with a SELECT 
    {
        $method = 'get'.ucfirst($key);
        $value = $this->data->$method();
        if ($value instanceof \DateTimeInterface) {
           return  $value->format('Y-m-d H:i:s');
        }

        return $value;
    }

    /**
     * Get the value of edit
     */ 
    public function getEdit()
    {
        return $this->edit;
    }
}