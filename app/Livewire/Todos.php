<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Todos')]
class Todos extends Component
{
    public $todo = '';

    public $todos = [];

    public function add()
    {
        if ($this->todo) {
            $this->todos[] = $this->todo;
            $this->todo = "";
        }
    }

    public function updated($property, $value) {
        // dd($property, $value);
        $this->$property = strtoupper($value);
    }

    public function mount() {
        $this->todos = [
            "Take out trash",
            "Do the dishes"
        ];
        $this->todo = "Shut the fuck-up";
    }

    public function render()
    {
        return view("livewire.todos");
    }
}
