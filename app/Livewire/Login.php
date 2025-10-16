<?php

namespace App\Livewire;

use Illuminate\Http\Request;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\Http; // Tambahkan ini



class Login extends Component
{

    public $username;

    public $password;
    public function render()
    {
        return view('livewire.login');
    }

    public function login()
    {
        $admin = \DB::table('admin')
                    ->where('username', $this->username)
                    ->where('password', $this->password) // Pastikan password di database tidak di-hash jika ini adalah perbandingan langsung
                    ->first();

        if ($admin) {
            session()->put('id', $admin->id);
            session()->put('nama', $admin->nama);
            session()->put('nama_pasar', $admin->nama_pasar);
            session()->put('id_pasar', $admin->id_pasar);
            session()->put('username', $admin->username);

            session()->save();

            return redirect()->route('home');
        } else {
            LivewireAlert::title('Gagal!')
                ->text('Username atau password salah.')
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }
}
