<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Posts')]
class Posts extends Component
{
    public $posts = [];

    public function mount() {
        $this->posts = [
                ["id" => 1, "title" => "Donec hendrerit tempor tellus.", "content" => "Pellentesque condimentum, magna ut suscipit hendrerit, ipsum augue ornare nulla, non luctus diam neque sit amet urna."],
                ["id" => 2, "title" => "Donec hendrerit tempor tellus.", "content" => "Pellentesque condimentum, magna ut suscipit hendrerit, ipsum augue ornare nulla, non luctus diam neque sit amet urna."],
                ["id" => 3, "title" => "Donec hendrerit tempor tellus.", "content" => "Pellentesque condimentum, magna ut suscipit hendrerit, ipsum augue ornare nulla, non luctus diam neque sit amet urna."],
                ["id" => 4, "title" => "Donec hendrerit tempor tellus.", "content" => "Pellentesque condimentum, magna ut suscipit hendrerit, ipsum augue ornare nulla, non luctus diam neque sit amet urna."],
                ["id" => 5, "title" => "Donec hendrerit tempor tellus.", "content" => "Pellentesque condimentum, magna ut suscipit hendrerit, ipsum augue ornare nulla, non luctus diam neque sit amet urna."],
        ];
    }

    public function delete($id) {
        dd($id, $this->posts);
    }

    public function render()
    {
        return view('livewire.posts');
    }
}
