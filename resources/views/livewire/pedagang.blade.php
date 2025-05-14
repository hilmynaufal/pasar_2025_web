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
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
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
    $(document).ready(function () {
        var data1 = @json($pedagang);
        var table = $('#pedagang').DataTable({
            "pageLength": 25,
            "data": data1,
            "columns": [
                { "data": "nama" },

                { "data": "kode_kios" },
                { "data": "id_kios" },
                {
                    "data": "tarif", "render": function (data, type, row) {
                        return '<b>Rp. ' + data;
                    }
                },
                {
                    "data": "nomor_identitas", "render": function (data, type, row) {
                        return data == "" ? "-" : data;
                    }
                },
                {
                    "data": "alamat", "render": function (data, type, row) {
                        return data == "" ? "-" : data;
                    }
                },
                { "data": "jenis_dagangan" },

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
            columnDefs: [{
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

    $('#pedagang tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child(format(row.data())).show();
            tr.addClass('shown');
        }
    });

</script>
@endscript