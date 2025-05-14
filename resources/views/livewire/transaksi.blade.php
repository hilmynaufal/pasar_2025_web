<div wire:key="jjj" class="container-scroller">
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
                                            <h3 class="font-weight-bold">Transaksi</h3>
                                            <p class="card-description">
                                                Semua transaksi di {{session('nama_pasar')}} pada Tanggal {{ $date }}
                                            </p>
                                        </div>
                                        <div class="col-12 col-xl-4">
                                            <div class="justify-content-end d-flex">
                                                <div class="flex-md-grow-1 flex-xl-grow-0">
                                                    <p class="card-description">
                                                        Pilih Tanggal
                                                    </p>
                                                    <input class="form-control form-control-sm text-primary"
                                                        wire:model="date" name="date" wire:key="ahay"
                                                        id="kt_datepicker_1" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap mb-5">
                                <div class="mr-5 mt-3">
                                    <p class="text-muted">Total Transaksi Hari ini</p>
                                    <h3 id="total_transaksi" class="text-primary fs-30 font-weight-medium">0</h3>
                                </div>
                                <div class="mr-5 mt-3">
                                    <p class="text-muted">Total Nominal hari ini</p>
                                    <h3 id="total_nominal" class="text-primary fs-30 font-weight-medium">Rp. 0</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table id="tagihan" class="table table-striped compact" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Id Transaksi</th>
                                                    <th>Status</th>
                                                    <th>Metode Pembayaran</th>
                                                    <th>Tanggal</th>
                                                    <th>Kode Kios</th>
                                                    <th>Pedagang</th>
                                                    <th> Nominal</th>
                                                    <th>Nama Pasar</th>
                                                    <th>Nama Distrik</th>
                                                    <th>Nama Petugas</th>
                                                    <th></th>
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

@script
<script>
    document.addEventListener('livewire:navigated', function () {

        var settings = {
            "url": "https://hirumi.xyz/pasar_2025_web/api/dashboard",
            "method": "POST",
            "timeout": 0,
            "data": {
                "tanggal": "{{$date}}",
                "nama_pasar": '{{ session('nama_pasar') }}'
            }
        };

        $.ajax(settings).done(function (response) {
            console.log(response);
            $('#total_transaksi').text(response['data']['jumlah_transaksi']);
            $('#total_nominal').text('Rp. ' + response['data']['total_nominal']);
        })

        var settings = {
            "url": "https://hirumi.xyz/pasar_2025_web/api/laporan",
            "method": "POST",
            "timeout": 0,
            "data": {
                "tanggal": "{{ $date }}",
                "nama_pasar": '{{ session('nama_pasar') }}'
            }
        };

        $.ajax(settings).done(function (response) {
            console.log(response);
            var table = $('#tagihan').DataTable({
                "pageLength": 25,
                "data": response['data'],
                "columns": [
                    { "data": "id" },
                    {
                        "data": "status", "render": function (data, type, row) {
                            var s = '<label class="badge badge-success">' + data + '</label>';
                            return s;
                        }
                    },
                    { "data": "metode_pembayaran" },
                    { "data": "tanggal_transaksi" },
                    { "data": "kode_kios", },
                    {
                        "data": "nama_pedagang", "render": function (data, type, row) {

                            return '<b>' + data + '</b>';
                        }
                    },
                    {
                        "data": "nominal_transaksi", "render": function (data, type, row) {
                            var s = 'Rp. ' + data;
                            return s;
                        }
                    },
                    { "data": "nama_pasar" },
                    { "data": "nama_distrik" },
                    { "data": "nama_petugas" },
                    {
                        "className": 'details-control',
                        "orderable": false,
                        "data": null,
                        "defaultContent": ''
                    }
                ],
                "order": [[0, 'asc']],
                "paging": true,
                "ordering": true,
                "info": false,
                "filter": true,
                "layout": {
                    // topStart: 'search',
                    "topEnd": 'search'
                },
                "columnDefs": [{
                    orderable: true,
                    className: 'select-checkbox',
                    targets: 0
                }],
                select: {
                    style: 'os',
                    selector: 'td:first-child'
                }
            });
        });

        var a = $("#kt_datepicker_1").flatpickr({
            "setDate": new Date(),
            "autoclose": true,
            "onChange": function (selectedDates, dateStr, instance) {
                console.log(dateStr);
                var settings = {
                    "url": "https://hirumi.xyz/pasar_2025_web/api/laporan",
                    "method": "POST",
                    "timeout": 0,
                    "data": {
                        "tanggal": dateStr,
                        "nama_pasar": '{{ session('nama_pasar') }}'
                    }
                };

                $.ajax(settings).done(function (response) {
                    $('#tagihan').DataTable().clear().rows.add(response['data']).draw();
                })

                //ganti juga stat Total
                var settings = {
                    "url": "https://hirumi.xyz/pasar_2025_web/api/dashboard",
                    "method": "POST",
                    "timeout": 0,
                    "data": {
                        "tanggal": dateStr,
                        "nama_pasar": '{{ session('nama_pasar') }}'
                    }
                };

                $.ajax(settings).done(function (response) {
                    console.log(response);
                    $('#total_transaksi').text(response['data']['jumlah_transaksi']);
                    $('#total_nominal').text('Rp. ' + response['data']['total_nominal']);
                })
            }
        });



    }, { once: true })

</script>
@endscript