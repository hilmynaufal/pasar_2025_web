<?php

namespace App\Livewire;

use Livewire\Component;

class NavBar extends Component
{

    public function logout()
    {
        session()->forget('id');
        session()->forget('nama');
        session()->forget('nama_pasar');
        session()->forget('id_pasar');
        session()->forget('username');
        return redirect()->route('login');
    }
    public function render()
    {
        return view('livewire.navbar');
    }
}
