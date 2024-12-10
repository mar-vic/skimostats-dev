<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Races')]
class CreatePost extends Component
{
    public $title = "";

    public $content = "";

    public function save() {
        dd("Aaaand we're saving!");
    }

    public function render()
    {
        return view('livewire.create-post');
    }
}
