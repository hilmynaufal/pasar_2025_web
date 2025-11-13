<div>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <livewire:nav-bar/>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_settings-panel.html -->
            @include('livewire.floating-button')
            @include('livewire.sidebar-right')
            <!-- partial -->
            <!-- partial:partials/_sidebar.html -->
            <livewire:sidebar />
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-0" style="margin-right:4px">
                                    <div class="grid-margin">
                                        <div class="row">
                                            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                                                <h3 class="font-weight-bold">Pedagang</h3>
                                                <p class="card-description">
                                                    Semua pedagang di {{session('nama_pasar')}}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <button wire:click="create" class="btn btn-primary">
                                            <i class="icon-plus"></i> Tambah Pedagang
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div wire:ignore class="table-responsive">
                                            <table id="pedagang" class="table table-striped compact" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Nama</th>
                                                        <th>Kode Kios</th>
                                                        <th>Id Kios</th>
                                                        <th>Tarif</th>
                                                        <th>No. Identitas</th>
                                                        <th>Alamat</th>
                                                        <th>Jenis Dagangan</th>
                                                        <th>Email</th>
                                                        <th>QR Code</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2021.
                            Premium <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin
                                template</a> from BootstrapDash. All rights reserved.</span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made
                            with <i class="ti-heart text-danger ml-1"></i></span>
                    </div>
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Distributed by <a
                                href="https://www.themewagon.com/" target="_blank">Themewagon</a></span>
                    </div>
                </footer>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <style>
    .modal.show {
        display: block !important;
    }
    .alert {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
    }
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .me-1 {
        margin-right: 0.25rem !important;
    }
    </style>

    <!-- Alert Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Modal Form -->
    @if($showModal)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editingPedagang ? 'Edit Pedagang' : 'Tambah Pedagang Baru' }}</h5>
                    <button type="button" class="close" wire:click="closeModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="save">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama">Nama Pedagang *</label>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                           wire:model="nama" id="nama" placeholder="Masukkan nama pedagang">
                                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kode_kios">Kode Kios *</label>
                                    <input type="text" class="form-control @error('kode_kios') is-invalid @enderror" 
                                           wire:model="kode_kios" id="kode_kios" placeholder="Masukkan kode kios">
                                    @error('kode_kios') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_kios">ID Kios *</label>
                                    <input type="text" class="form-control @error('id_kios') is-invalid @enderror" 
                                           wire:model="id_kios" id="id_kios" placeholder="Masukkan ID kios">
                                    @error('id_kios') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tarif">Tarif (Rp) *</label>
                                    <input type="number" class="form-control @error('tarif') is-invalid @enderror" 
                                           wire:model="tarif" id="tarif" placeholder="Masukkan tarif" min="0">
                                    @error('tarif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nomor_identitas">Nomor Identitas</label>
                                    <input type="text" class="form-control @error('nomor_identitas') is-invalid @enderror" 
                                           wire:model="nomor_identitas" id="nomor_identitas" placeholder="Masukkan nomor identitas">
                                    @error('nomor_identitas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           wire:model="email" id="email" placeholder="Masukkan email">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_dagangan">Jenis Dagangan *</label>
                                    <input type="text" class="form-control @error('jenis_dagangan') is-invalid @enderror" 
                                           wire:model="jenis_dagangan" id="jenis_dagangan" placeholder="Masukkan jenis dagangan">
                                    @error('jenis_dagangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                              wire:model="alamat" id="alamat" rows="3" placeholder="Masukkan alamat"></textarea>
                                    @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                    <button type="button" class="btn btn-primary" wire:click="save">
                        {{ $editingPedagang ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal QR Code Preview -->
    @if($showQrModal)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">QR Code - {{ $qrPedagangNama }}</h5>
                    <button type="button" class="close" wire:click="closeQrModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ $qrCodeUrl }}" alt="QR Code" class="img-fluid" style="max-width: 300px;">
                    <div class="mt-3">
                        <a href="{{ $qrCodeUrl }}" download="qr_{{ $qrPedagangNama }}.png" class="btn btn-primary">
                            <i class="icon-download"></i> Download QR Code
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeQrModal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>


@script
<script>
    $(document).ready(function () {
        initializeDataTable();
    });

    // Fungsi untuk edit pedagang
    window.editPedagang = function(id) {
        @this.call('edit', id);
    };

    // Fungsi untuk delete pedagang dengan konfirmasi
    window.deletePedagang = function(id) {
        if (confirm('Apakah Anda yakin ingin menghapus pedagang ini?')) {
            @this.call('delete', id);
        }
    };

    // Fungsi untuk generate QR code
    window.generateQr = function(id) {
        @this.call('generateQr', id);
    };

    // Fungsi untuk view QR code
    window.viewQr = function(id, qrCodeFile, nama) {
        @this.call('viewQr', id, qrCodeFile, nama);
    };

    
    // Close modal when clicking outside
    $(document).on('click', '.modal', function(e) {
        if (e.target === this) {
            @this.call('closeModal');
        }
    });

    // Function to initialize DataTable
    function initializeDataTable() {
        // console.log('initializeDataTable');
        $('#pedagang').DataTable().destroy();
        
        var data1 = @json($pedagang->toArray());
        var table = $('#pedagang').DataTable({
            "pageLength": 25,
            "data": data1,
            "columns": [
                { "data": "nama", "render": function (data, type, row) {
                    if (data == null || data == "" || data == ' ') {
                        return "<span style='color: grey;'><i>(Nama Kosong)</i></span>"
                    } else {
                        return data;
                    }
                } },
                { "data": "kode_kios" },
                { "data": "id_kios" },
                {
                    "data": "tarif", "render": function (data, type, row) {
                        return '<b>Rp. ' + data;
                    }
                },
                {
                    "data": "nomor_identitas", "render": function (data, type, row) {
                        if (data == null || data == "" || data == ' ') {
                            return "<span style='color: grey;'><i>(Nomor Identitas Kosong)</i></span>"
                        } else {
                            return data;
                        }
                    }
                },
                {
                    "data": "alamat", "render": function (data, type, row) {
                        if (data == null || data == "" || data == ' ') {
                            return "<span style='color: grey;'><i>(Alamat Kosong)</i></span>"
                        } else {
                            return data;
                        }
                    }
                },
                { "data": "jenis_dagangan", "render": function (data, type, row) {
                    if (data == null || data == "" || data == ' ') {
                        return "<span style='color: grey;'><i>(Jenis Dagangan Kosong)</i></span>"
                    } else {
                        return data;
                    }
                } },
                {
                    "data": "email", "render": function (data, type, row) {
                        if (data == null || data == "" || data == ' ') {
                            return "<span style='color: grey;'><i>(Email Kosong)</i></span>"
                        } else {
                            return data;
                        }
                    }
                },
                {
                    "className": 'text-center',
                    "orderable": false,
                    "data": null,
                    "render": function (data, type, row) {
                        if (row.qr_code_file && row.qr_code_file != '' && row.qr_code_file != ' ' && row.qr_code_file != null) {
                            return '<button class="btn btn-sm btn-success me-1" onclick="viewQr(' + row.id + ', \'' + row.qr_code_file + '\', \'' + row.nama + '\')"><i class="icon-eye"></i></button>' +
                                   '<button class="btn btn-sm btn-primary" onclick="generateQr(' + row.id + ')"><i class="icon-refresh"></i></button>';
                        } else {
                            return '<button class="btn btn-sm btn-primary" onclick="generateQr(' + row.id + ')"><i class="icon-layers"></i></button>';
                        }
                    }
                },
                {
                    "className": 'text-center',
                    "orderable": false,
                    "data": null,
                    "render": function (data, type, row) {
                        return '<button class="btn btn-sm btn-warning me-1" onclick="editPedagang(' + row.id + ')"><i class="icon-pencil"></i></button><button class="btn btn-sm btn-danger" onclick="deletePedagang(' + row.id + ')"><i class="icon-trash"></i></button>';
                    }
                }
            ],
            "order": [[0, 'asc']],
            "paging": true,
            "ordering": true,
            "info": false,
            "filter": true,
            "layout": {
                "topEnd": 'search'
            }
        });
    }
    
    $wire.on('dataTableRefresh', () => {
        console.log('dataTableRefresh');
        // $('#pedagang').DataTable().ajax.reload();
        initializeDataTable();
    });
    

</script>
@endscript