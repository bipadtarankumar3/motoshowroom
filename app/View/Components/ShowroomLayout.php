<?php

namespace App\View\Components;

use App\Models\Showroom;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ShowroomLayout extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Showroom $showroom,
        public ?string $title = null,
        public ?string $metaDescription = null
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('layouts.showroom');
    }
}
