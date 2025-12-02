<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class NavBar extends Component
{

    public $isPasswordModalOpen = false;
    public $current_password = '';
    public $new_password = '';
    public $repeat_password = '';

    protected $rules = [
        'current_password' => 'required',
        'new_password' => 'required|min:6',
        'repeat_password' => 'required|same:new_password',
    ];

    protected $messages = [
        'current_password.required' => 'Password saat ini harus diisi',
        'new_password.required' => 'Password baru harus diisi',
        'new_password.min' => 'Password baru minimal 6 karakter',
        'repeat_password.required' => 'Ulangi password harus diisi',
        'repeat_password.same' => 'Password baru dan ulangi password harus sama',
    ];

    public function openPasswordModal()
    {
        $this->isPasswordModalOpen = true;
        $this->reset(['current_password', 'new_password', 'repeat_password']);
        $this->resetValidation();
    }

    public function closePasswordModal()
    {
        $this->isPasswordModalOpen = false;
        $this->reset(['current_password', 'new_password', 'repeat_password']);
        $this->resetValidation();
    }

    public function updatePassword()
    {
        $this->validate();

        try {
            $response = Http::post(env('API_BASE_URL') . '/api/update-password', [
                'id' => session('id'),
                'current_password' => $this->current_password,
                'new_password' => $this->new_password,
            ]);

            $data = $response->json();

            if ($data['status'] == 1) {
                LivewireAlert::title('Berhasil!')
                    ->text($data['message'])
                    ->success()
                    ->toast()
                    ->position('top-end')
                    ->show();
                $this->closePasswordModal();
            } else {
                LivewireAlert::title('Gagal!')
                    ->text($data['message'])
                    ->error()
                    ->toast()
                    ->position('top-end')
                    ->show();
            }
        } catch (\Exception $e) {
            LivewireAlert::title('Gagal!')
                ->text('Terjadi kesalahan: ' . $e->getMessage())
                ->error()
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

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
