<?php

namespace App\View\Components;

use Illuminate\View\Component;

class waiting-3dprinting-dialog extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.waiting-3dprinting-dialog');
    }
}
