<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public string $name;

    public mixed $options;

    public string $label;

    public mixed $value;

    public string $placeholder;

    public string $icon;

    public bool $required;

    public bool $disabled;

    public bool $multiple;

    public ?string $help;

    public ?string $wrapperClass;

    public string $optionValue;

    public string $optionLabel;
    

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name,
        mixed $options = [],
        string $label = '',
        mixed $value = null,
        string $placeholder = 'Sélectionner...',
        string $icon = '',
        bool $required = false,
        bool $disabled = false,
        bool $multiple = false,
        ?string $help = null,
        ?string $wrapperClass = null,
        string $optionValue = 'id',
        string $optionLabel = 'name',
    ) 
    {
        $this->name = $name;
        $this->options = $options;
        $this->label = $label;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->icon = $icon;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->multiple = $multiple;
        $this->help = $help;
        $this->wrapperClass = $wrapperClass;
        $this->optionValue = $optionValue;
        $this->optionLabel = $optionLabel;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.select', $this->data());
    }
}