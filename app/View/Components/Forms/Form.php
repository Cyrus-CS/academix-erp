<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\Component;

class Form extends Component
{
    public string $resource;
    public ?string $enctype;
    public bool $isEdit;
    public string $action;
    public array $routeParameters = [];
    public string $autocomplete;
    public Model $model;
    
    /**
     * Create a new component instance.
     */
    public function __construct(
        Model $model,
        string $resource,
        ?string $enctype = null,
        array $routeParameters = [],
        string $autocomplete = 'off',
    )
    {
        $this->resource = $resource;
        $this->autocomplete = $autocomplete;
        $this->routeParameters = $routeParameters;
        $this->enctype = $enctype;
        $this->model = $model;
        $this->isEdit = $model->exists;
        $this->action = $this->isEdit ? route("{$this->resource}.update", $this->model) : route("{$this->resource}.store", $this->routeParameters);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.form');
    }
}