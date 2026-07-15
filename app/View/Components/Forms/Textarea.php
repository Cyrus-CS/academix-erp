<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    public string $name = '';
    public ?string $label;
    public mixed $value;
    public ?string $icon;
    public ?string $help;
    public int  $rows = 4; 
    public ?string $wrapperClass;
    public ?string $placeholder;
    public bool $disabled = false;
    public bool $required = false;   
    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name = '',
        int $rows = 4,
        ?string $label = null,
        ?string $icon = null,
        ?string $help = null,
        bool $disabled = false,
        bool $required = false,
        mixed $value = null,
        ?string $wrapperClass = null,
        ?string $placeholder = null
    ) {
        $this->name = $name;
        $this->rows = $rows;
        $this->label = $label;
        $this->icon = $icon;
        $this->help = $help;
        $this->value = $value;
        $this->wrapperClass = $wrapperClass;
        $this->placeholder = $placeholder;
        $this->disabled = $disabled;
        $this->required = $required;
    }


    /**
     * Get the view / contents that reprint $rowsesent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.textarea');
    }
}