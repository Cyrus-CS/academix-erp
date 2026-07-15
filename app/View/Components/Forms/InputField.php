<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputField extends Component
{
    public string $name;
    public string $type = 'text';
    public mixed $value;
    public string $label;

    public ?string $help;
    public ?string $placeholder;
    public ?string $class;
    public ?bool $required;
    public ?bool $disabled;
    public ?string $icon;
    public ?bool $readonly;

    public ?bool $multiple;
    
    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        string $type = 'text',
        mixed $value = '', 
        string $label = '',
        string $help = null,
        string $class = null,
        string $icon = null,
        string $placeholder = null,
        bool $required = false,
        bool $disabled = false,
        bool $readonly = false,
        bool $multiple = false,
    )
    {
        $this->name = $name;
        $this->label = $label ?: ucfirst($name); 
        $this->type  = $type;
        $this->class = $class;
        $this->icon = $icon;
        $this->placeholder = $placeholder;
        $this->value = $value;
        $this->help = $help;
        $this->multiple = $multiple;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->readonly = $readonly;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.input-field', $this->data());
    }
}