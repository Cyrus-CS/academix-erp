<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Help extends Component
{
    public string $name = '';
    public ?string $help;
    /**
     * Create a new component instance.
     */
    public function __construct(string $name, ?string $help = null)
    {
        $this->name = $name;
        $this->help = $help;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.help');
    }
}