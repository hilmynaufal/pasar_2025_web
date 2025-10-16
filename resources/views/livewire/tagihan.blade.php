<div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <livewire:nav-bar />
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
                                            <h3 class="font-weight-bold">Tagihan</h3>
                                            <p class="card-description">
                                                Semua tagihan di {{session('nama_pasar')}} pada Tanggal {{ $date }}
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
                                    <p class="text-muted">Total Tagihan Belum Dibayar</p>
                                    <h3 id="total_belum_dibayar" class="text-primary fs-30 font-weight-medium">0</h3>
                                </div>
                                <div class="mr-5 mt-3">
                                    <p class="text-muted">Total Tagihan Sudah Dibayar</p>
                                    <h3 id="total_sudah_dibayar" class="text-primary fs-30 font-weight-medium">0</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table id="tagihan" class="table table-striped compact" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Pedagang</th>
                                                    <th>Status</th>
                                                    <th>Pasar</th>
                                                    <th>Tanggal</th>
                                                    <th>Kode Kios</th>
                                                    <th>Tipe</th>
                                                    <th> Tarif</th>
                                                    <th>Id Transaksi</th>
                                                    <th>Salesman</th>
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


@script
<script>
    document.addEventListener('livewire:navigated', function () {

        var settings = {
            "url": "{{ env('APP_URL') }}/tagihan_stat",
            "method": "POST",
            "timeout": 0,
            "data": {
                "tanggal": "{{$date}}",
                "nama_pasar": '{{ session('nama_pasar') }}'
            }
        };

        $.ajax(settings).done(function (response) {
            console.log(response);
            $('#total_sudah_dibayar').text(response['data']['totalSudahDibayar']);
            $('#total_belum_dibayar').text(response['data']['totalBelumDibayar']);
        })

        var settings = {
            "url": "{{ env('APP_URL') }}/tagihan",
            "method": "POST",
            "timeout": 0,
            "data": {
                "tanggal": "{{ $date }}",
                "nama_pasar": '{{ session('nama_pasar') }}'
            }
        };

        $.ajax(settings).done(function (response) {

            console.log(response);
            var table = new DataTable('#tagihan', {
                // layout: {
                //     topStart: {
                //         buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                //     }
                // },
                layout: {
                    topStart: {
                        buttons: [
                            {
                                extend: 'collection',
                                text: 'Export',
                                buttons: ['copy', 'excel', 'csv', 'pdf', 'print']
                            }
                        ]
                    }
                },
                "pageLength": 25,
                "data": response['data'],
                "columns": [
                    {
                        "data": "pedagang"
                    },
                    {
                        "data": "status",
                        "render": function (data, type, row) {
                            var s = data == 0 ? '<label class="badge badge-danger">Belum Dibayar</label>' : '<label class="badge badge-success">Sudah Dibayar</label>';
                            return s;
                        }
                    },
                    { "data": "merchant_id" },
                    { "data": "tanggal_tagihan" },
                    { "data": "id_kios" },
                    {
                        "data": "invoice_type", "render": function (data, type, row) {
                            return '<b>' + data + '</b>';
                        }
                    },
                    {
                        "data": "tarif",
                        "render": function (data, type, row) {
                            var s = 'Rp. ' + data;
                            return s;
                        }
                    },
                    { "data": "transaction_id" },
                    { "data": "salesman" },
                    {
                        "className": 'details-control',
                        "orderable": false,
                        "data": null,
                        "defaultContent": ''
                    }
                ],
                // "order": [[0, 'asc']],
                // "paging": true,
                // "ordering": true,
                // "info": false,
                // "filter": true,
                "select": true,
                "columnDefs": [{
                    "orderable": true,
                    "className": 'select-checkbox',
                    "targets": 0
                }],
                "select": {
                    "style": 'os',
                    "selector": 'td:first-child'
                }
            });
        });

        var a = $("#kt_datepicker_1").flatpickr({
            "setDate": new Date(),
            "autoclose": true,
            "onChange": function (selectedDates, dateStr, instance) {
                console.log(dateStr);
                var settings = {
                    "url": "{{ env('APP_URL') }}/tagihan",
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

                var settings = {
                    "url": "{{ env('APP_URL') }}/tagihan_stat",
                    "method": "POST",
                    "timeout": 0,
                    "data": {
                        "tanggal": dateStr,
                        "nama_pasar": '{{ session('nama_pasar') }}'
                    }
                };

                $.ajax(settings).done(function (response) {
                    console.log(response);
                    $('#total_sudah_dibayar').text(response['data']['totalSudahDibayar']);
                    $('#total_belum_dibayar').text(response['data']['totalBelumDibayar']);
                })

            }
        });
    });

</script>
@endscript