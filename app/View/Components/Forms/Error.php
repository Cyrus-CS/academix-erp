<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Error extends Component
{
    public string $name;
    /**
     * Create a new component instance.
     */
    public function __construct(string $name = '')
    {
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.error', $this->data());
    }
}