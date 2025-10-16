<?php

namespace App\Livewire;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Pedagang extends Component
{
    use WithPagination;
    
    public $pedagang;
    public $showModal = false;
    public $editingPedagang = null;
    
    // Form fields
    public $nama = '';
    public $kode_kios = '';
    public $id_kios = '';
    public $tarif = '';
    public $nomor_identitas = '';
    public $alamat = '';
    public $jenis_dagangan = '';
    public $email = '';
    
    protected $rules = [
        'nama' => 'required|string|max:255',
        'kode_kios' => 'required|string|max:50',
        'id_kios' => 'required|string|max:50',
        'tarif' => 'required|numeric|min:0',
        'nomor_identitas' => 'nullable|string|max:50',
        'alamat' => 'nullable|string|max:500',
        'jenis_dagangan' => 'required|string|max:255',
        'email' => 'nullable|email|max:255'
    ];
    
    protected function rules()
    {
        $rules = $this->rules;
        
        if ($this->editingPedagang) {
            // Saat edit, pastikan kode_kios dan id_kios unik kecuali untuk record yang sedang diedit
            $rules['kode_kios'] .= '|unique:pedagang,kode_kios,' . $this->editingPedagang;
            $rules['id_kios'] .= '|unique:pedagang,id_kios,' . $this->editingPedagang;
        } else {
            // Saat create, pastikan kode_kios dan id_kios unik
            $rules['kode_kios'] .= '|unique:pedagang,kode_kios';
            $rules['id_kios'] .= '|unique:pedagang,id_kios';
        }
        
        return $rules;
    }
    
    public function mount() {
        $this->fetchDataPedagang();
    }
    
    public function fetchDataPedagang() {
        $nama_pasar = session('nama_pasar');
        
        if (empty($nama_pasar)) {
            $this->pedagang = DB::table('pedagang')
                ->select('*')
                ->orderBy('nama', 'asc')
                ->get();
        } else {
            $this->pedagang = DB::table('pedagang')
                ->select('*')
                ->where('nama_pasar', $nama_pasar)
                ->orderBy('nama', 'asc')
                ->get();
        }
        
    }
    
    public function refreshDataTable() {
        $this->fetchDataPedagang();
        $this->dispatch('dataTableRefresh');
    }
    
    public function create() {
        $this->resetForm();
        $this->showModal = true;
        $this->editingPedagang = null;
    }
    
    public function edit($id) {
        $pedagang = collect($this->pedagang)->firstWhere('id', $id);
        if ($pedagang) {
            $this->editingPedagang = $id;
            $this->nama = $pedagang->nama;
            $this->kode_kios = $pedagang->kode_kios;
            $this->id_kios = $pedagang->id_kios;
            $this->tarif = $pedagang->tarif;
            $this->nomor_identitas = $pedagang->nomor_identitas ?? '';
            $this->alamat = $pedagang->alamat ?? '';
            $this->jenis_dagangan = $pedagang->jenis_dagangan;
            $this->email = $pedagang->email ?? '';
            $this->showModal = true;
        }
    }
    
    public function save() {
        $this->validate($this->rules());
        
        $data = [
            'nama' => $this->nama,
            'kode_kios' => $this->kode_kios,
            'id_kios' => $this->id_kios,
            'tarif' => $this->tarif,
            'nomor_identitas' => $this->nomor_identitas,
            'alamat' => $this->alamat,
            'jenis_dagangan' => $this->jenis_dagangan,
            'email' => $this->email,
            'nama_pasar' => session('nama_pasar'),
            'updated_at' => now()
        ];
        
        try {
            if ($this->editingPedagang) {
                // Update existing pedagang
                $result = DB::table('pedagang')
                    ->where('id', $this->editingPedagang)
                    ->update($data);
                
                if ($result) {
                    session()->flash('success', 'Pedagang berhasil diperbarui!');
                } else {
                    session()->flash('error', 'Gagal memperbarui pedagang!');
                    return;
                }
            } else {
                // Create new pedagang
                // $data['created_at'] = now();
                $result = DB::table('pedagang')->insert($data);
                
                if ($result) {
                    session()->flash('success', 'Pedagang berhasil ditambahkan!');
                } else {
                    session()->flash('error', 'Gagal menambahkan pedagang!');
                    return;
                }
            }
            
            $this->refreshDataTable();
            $this->closeModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function delete($id) {
        try {
            $result = DB::table('pedagang')->where('id', $id)->delete();
            
            if ($result) {
                session()->flash('success', 'Pedagang berhasil dihapus!');
                $this->refreshDataTable();
            } else {
                session()->flash('error', 'Gagal menghapus pedagang!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function closeModal() {
        $this->showModal = false;
        $this->resetForm();
        $this->editingPedagang = null;
    }
    
    public function resetForm() {
        $this->nama = '';
        $this->kode_kios = '';
        $this->id_kios = '';
        $this->tarif = '';
        $this->nomor_identitas = '';
        $this->alamat = '';
        $this->jenis_dagangan = '';
        $this->email = '';
        $this->resetErrorBag();
    }
    
    public function render()
    {
        return view('livewire.pedagang');
    }
}
