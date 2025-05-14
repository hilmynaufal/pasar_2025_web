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

    public function login(Request $request)
    {

        $response = Http::post("https://hirumi.xyz/pasar_2025_web/api" . '/admin', [
            'username' => $this->username,
            'password' => $this->password,
        ]);

        $jsonReponse = $response->json();

        if ($jsonReponse['status'] == 0) {
            // LivewireAlert::title('Gagal!')
            // ->text($jsonReponse['message'])
            // ->error()
            // ->toast()
            // ->position('top-end')
            // ->show();
        } else {

            $data = $jsonReponse['data'];

            session()->put('id', $data['id']);
            session()->put('nama', $data['nama']);
            session()->put('nama_pasar', $data['nama_pasar']);
            session()->put('id_pasar', $data['id_pasar']);
            session()->put('username', $data['username']);

            session()->save();

            return redirect()->route('home');
        }
    }
}
