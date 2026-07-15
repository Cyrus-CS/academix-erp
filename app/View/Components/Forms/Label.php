<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Label extends Component
{
    public ?string $label;
    public ?string $icon;
    public string $name;
    public bool $required = false;
    /**
     * Create a new component instance.
     */
    public function __construct(string $name = "", string $label = null, ?string $icon = null, bool $required = false)
    {
        $this->name = $name;
        $this->required = $required;
        $this->icon = $icon;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.label', $this->data());
    }
}