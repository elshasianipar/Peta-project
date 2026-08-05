<?php

namespace App\Livewire\Admin\Articles;

use livewire\attributes\layout;
use Livewire\Component;

#[layout('layouts::app')]
class Create extends Component
{
    public function render()
    {
        return view('livewire.admin.articles.create');
    }
}
