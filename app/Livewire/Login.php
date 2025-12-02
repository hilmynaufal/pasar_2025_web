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
        // Cari admin berdasarkan username saja
        $admin = \DB::table('admin')
                    ->where('username', $this->username)
                    ->first();

        if ($admin) {
            $passwordValid = false;

            // Cek apakah password di database sudah hash (bcrypt)
            // Bcrypt hash dimulai dengan "$2y$" atau "$2a$" atau "$2b$"
            if (password_verify($this->password, $admin->password)) {
                // Password cocok dengan hash
                $passwordValid = true;
            }
            // Jika gagal dengan hash, coba plain text (backward compatibility)
            elseif ($admin->password === $this->password) {
                // Password masih plain text, login berhasil
                $passwordValid = true;
            }

            if ($passwordValid) {
                // Set session
                session()->put('id', $admin->id);
                session()->put('nama', $admin->nama);
                session()->put('nama_pasar', $admin->nama_pasar);
                session()->put('id_pasar', $admin->id_pasar);
                session()->put('username', $admin->username);
                session()->put('role', $admin->role ?? 'admin');

                session()->save();

                return redirect()->route('home');
            }
        }

        // Login gagal
        LivewireAlert::title('Gagal!')
            ->text('Username atau password salah.')
            ->error()
            ->toast()
            ->position('top-end')
            ->show();
    }
}
