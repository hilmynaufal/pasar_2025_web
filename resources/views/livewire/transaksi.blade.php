<div wire:key="jjj" class="container-scroller">
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
                                        <div class="col-12 mb-4">
                                            <h3 class="font-weight-bold">Transaksi</h3>
                                            <p class="card-description">
                                                Semua transaksi di {{session('nama_pasar')}} pada Tanggal {{ $date }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                                            <p class="card-description">
                                                Pilih Tanggal
                                            </p>
                                            <input class="form-control form-control-sm text-primary"
                                                wire:model="date" name="date" wire:key="ahay"
                                                id="kt_datepicker_1" />
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                                            <p class="card-description">
                                                Filter Pasar
                                            </p>
                                            <select class="form-control form-control-sm" id="filter_pasar" wire:model="filter_pasar">
                                                @foreach($pasar_options as $pasar)
                                                    <option value="{{ $pasar }}">{{ $pasar }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                                            <p class="card-description">
                                                Filter Petugas
                                            </p>
                                            <select class="form-control form-control-sm" id="filter_petugas" wire:model="filter_petugas">
                                                <option value="">Semua Petugas</option>
                                                @if(!empty($petugas_options))
                                                    @foreach($petugas_options as $petugas)
                                                        <option value="{{ $petugas['id_petugas'] ?? '' }}">{{ $petugas['nama_petugas'] ?? 'Unknown' }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                                            <p class="card-description">
                                                Filter Distrik
                                            </p>
                                            <select class="form-control form-control-sm" id="filter_distrik" wire:model="filter_distrik">
                                                <option value="">Semua Distrik</option>
                                                @foreach($distrik_options as $distrik)
                                                    <option value="{{ $distrik }}">{{ $distrik }}</option>
                                                @endforeach
                                            </select>
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
                                                    @if(session('role') === 'superadmin')
                                                        <th>Aksi</th>
                                                    @endif
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
        // Function to get current filter values
        function getFilterData() {
            return {
                "tanggal": $("#kt_datepicker_1").val() || "{{$date}}",
                "nama_pasar": $("#filter_pasar").val() || "",
                "id_petugas": $("#filter_petugas").val() || "",
                "nama_distrik": $("#filter_distrik").val() || ""
            };
        }

        // Function to update table with filters
        function updateTable() {
            var filterData = getFilterData();

            $.ajax({
                "url": "{{ env('APP_URL') }}/laporan",
                "method": "POST",
                "timeout": 0,
                "data": filterData
            }).done(function (response) {
                $('#tagihan').DataTable().clear().rows.add(response['data']).draw();
            });
        }

        // Function to update statistics with filters
        function updateStats() {
            var filterData = getFilterData();

            function number_format(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            $.ajax({
                "url": "{{ env('APP_URL') }}/dashboard",
                "method": "POST",
                "timeout": 0,
                "data": filterData
            }).done(function (response) {
                console.log(response);
                $('#total_transaksi').text(response['data']['jumlah_transaksi']);
                $('#total_nominal').text('Rp. ' + number_format(response['data']['total_nominal']));
            });
        }

        // Function to calculate status breakdown from DataTable data
        function calculateStatusBreakdown(table) {
            var data = table.rows({ search: 'applied' }).data();
            var breakdown = {};

            // Process each row
            for (var i = 0; i < data.length; i++) {
                var row = data[i];
                var status = row.status || 'Unknown';
                var nominal = parseFloat(row.nominal_transaksi) || 0;

                if (!breakdown[status]) {
                    breakdown[status] = {
                        count: 0,
                        total: 0
                    };
                }

                breakdown[status].count++;
                breakdown[status].total += nominal;
            }

            return breakdown;
        }

        // Function to add statistics sheet to Excel export
        function addStatisticsSheet(xlsx, stats, statusBreakdown) {
            var sheetId = 2;

            // Build status breakdown rows
            var statusRows = '';
            var rowNum = 13;
            for (var status in statusBreakdown) {
                if (statusBreakdown.hasOwnProperty(status)) {
                    var data = statusBreakdown[status];
                    var formattedTotal = data.total.toLocaleString('id-ID');
                    statusRows += '<row r="' + rowNum + '">' +
                        '<c t="inlineStr" r="A' + rowNum + '"><is><t>' + status + '</t></is></c>' +
                        '<c t="inlineStr" r="B' + rowNum + '"><is><t>' + data.count + '</t></is></c>' +
                        '<c t="inlineStr" r="C' + rowNum + '"><is><t>Rp. ' + formattedTotal + '</t></is></c>' +
                        '</row>';
                    rowNum++;
                }
            }

            // Build statistics sheet XML
            var statsXML = '<' + '?xml version="1.0" encoding="UTF-8" standalone="yes"?' + '>' +
                '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" ' +
                'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' +
                '<sheetData>' +
                '<row r="1"><c t="inlineStr" r="A1" s="2"><is><t>LAPORAN STATISTIK TRANSAKSI</t></is></c></row>' +
                '<row r="2"><c t="inlineStr" r="A2" s="2"><is><t>Sistem Informasi Pasar ' + '{{ session("nama_pasar") }}' + '</t></is></c></row>' +
                '<row r="3"></row>' +
                '<row r="4"><c t="inlineStr" r="A4" s="2"><is><t>FILTER</t></is></c></row>' +
                '<row r="5"><c t="inlineStr" r="A5"><is><t>Tanggal</t></is></c>' +
                '<c t="inlineStr" r="B5"><is><t>' + stats.tanggal + '</t></is></c></row>' +
                '<row r="6"><c t="inlineStr" r="A6"><is><t>Pasar</t></is></c>' +
                '<c t="inlineStr" r="B6"><is><t>' + stats.pasar + '</t></is></c></row>' +
                '<row r="7"><c t="inlineStr" r="A7"><is><t>Petugas</t></is></c>' +
                '<c t="inlineStr" r="B7"><is><t>' + stats.petugas + '</t></is></c></row>' +
                '<row r="8"><c t="inlineStr" r="A8"><is><t>Distrik</t></is></c>' +
                '<c t="inlineStr" r="B8"><is><t>' + stats.distrik + '</t></is></c></row>' +
                '<row r="9"></row>' +
                '<row r="10"><c t="inlineStr" r="A10" s="2"><is><t>RINGKASAN TOTAL</t></is></c></row>' +
                '<row r="11"><c t="inlineStr" r="A11"><is><t>Total Transaksi</t></is></c>' +
                '<c t="inlineStr" r="B11"><is><t>' + stats.totalTransaksi + '</t></is></c></row>' +
                '<row r="12"><c t="inlineStr" r="A12"><is><t>Total Nominal</t></is></c>' +
                '<c t="inlineStr" r="B12"><is><t>' + stats.totalNominal + '</t></is></c></row>' +
                '<row r="13"></row>' +
                '<row r="14"><c t="inlineStr" r="A14" s="2"><is><t>BREAKDOWN PER STATUS</t></is></c></row>' +
                '<row r="15">' +
                '<c t="inlineStr" r="A15" s="2"><is><t>Status</t></is></c>' +
                '<c t="inlineStr" r="B15" s="2"><is><t>Jumlah</t></is></c>' +
                '<c t="inlineStr" r="C15" s="2"><is><t>Total Nominal</t></is></c>' +
                '</row>' +
                statusRows +
                '</sheetData>' +
                '</worksheet>';

            // Add the statistics sheet XML
            xlsx.xl.worksheets['sheet2.xml'] = $.parseXML(statsXML);

            // Update [Content_Types].xml
            var contentTypes = xlsx['[Content_Types].xml'];
            $('Override[PartName="/xl/worksheets/sheet1.xml"]', contentTypes)
                .clone()
                .attr('PartName', '/xl/worksheets/sheet2.xml')
                .insertAfter($('Override[PartName="/xl/worksheets/sheet1.xml"]', contentTypes));

            // Update xl/_rels/workbook.xml.rels
            var rels = xlsx.xl._rels['workbook.xml.rels'];
            var maxRid = 0;
            $('Relationship', rels).each(function() {
                var rid = $(this).attr('Id').replace('rId', '');
                maxRid = Math.max(maxRid, parseInt(rid));
            });

            $('Relationship[Target="worksheets/sheet1.xml"]', rels)
                .clone()
                .attr('Id', 'rId' + (maxRid + 1))
                .attr('Target', 'worksheets/sheet2.xml')
                .insertAfter($('Relationship[Target="worksheets/sheet1.xml"]', rels));

            // Update xl/workbook.xml
            var workbook = xlsx.xl['workbook.xml'];
            $('sheets sheet:first', workbook)
                .clone()
                .attr('name', 'Statistik')
                .attr('sheetId', sheetId)
                .attr('r:id', 'rId' + (maxRid + 1))
                .appendTo($('sheets', workbook));
        }

        // Initial load - statistics
        var settings = {
            "url": "{{ env('APP_URL') }}/dashboard",
            "method": "POST",
            "timeout": 0,
            "data": {
                "tanggal": "{{$date}}",
                "nama_pasar": '{{ session('nama_pasar') }}'
            }
        };

        $.ajax(settings).done(function (response) {
            console.log(response);

            function number_format(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            $('#total_transaksi').text(response['data']['jumlah_transaksi']);
            $('#total_nominal').text('Rp. ' + number_format(response['data']['total_nominal']));
        })

        // Initial load - table
        var settings = {
            "url": "{{ env('APP_URL') }}/laporan",
            "method": "POST",
            "timeout": 0,
            "data": {
                "tanggal": "{{ $date }}",
                "nama_pasar": '{{ session('nama_pasar') }}'
            }
        };

        $.ajax(settings).done(function (response) {
            console.log(response);
            
            // Build base columns array
            var baseColumns = [
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
                { "data": "nama_petugas" }
            ];
            
            // Check if superadmin and add action column (check if action header exists)
            var headerCount = $('#tagihan thead tr th').length;
            if (headerCount > 10) {
                baseColumns.push({
                    "className": 'text-center',
                    "orderable": false,
                    "data": null,
                    "render": function (data, type, row) {
                        var pedagangName = String(row.nama_pedagang || '').replace(/'/g, "\\'").replace(/"/g, "&quot;");
                        return '<button class="btn btn-danger btn-sm btn-hapus-transaksi" data-id="' + row.id + '" data-pedagang="' + pedagangName + '">' +
                               '<i class="mdi mdi-delete"></i> Hapus' +
                               '</button>';
                    }
                });
            }
            
            var table = new DataTable('#tagihan', {
                layout: {
                    topStart: {
                        buttons: [
                            {
                                extend: 'collection',
                                text: 'Export',
                                buttons: [
                                    'copy',
                                    {
                                        extend: 'excelHtml5',
                                        text: 'Excel',
                                        title: 'Laporan Transaksi',
                                        exportOptions: {
                                            columns: ':visible:not(:last-child)'
                                        },
                                        customize: function(xlsx) {
                                            // Get current statistics
                                            var stats = {
                                                tanggal: $("#kt_datepicker_1").val() || "{{$date}}",
                                                pasar: $("#filter_pasar option:selected").text() || $("#filter_pasar").val() || 'Semua Pasar',
                                                petugas: $("#filter_petugas option:selected").text() || 'Semua Petugas',
                                                distrik: $("#filter_distrik").val() || 'Semua Distrik',
                                                totalTransaksi: $('#total_transaksi').text(),
                                                totalNominal: $('#total_nominal').text()
                                            };

                                            // Calculate status breakdown from current table data
                                            var statusBreakdown = calculateStatusBreakdown(table);

                                            // Add statistics sheet
                                            addStatisticsSheet(xlsx, stats, statusBreakdown);

                                            // Rename main sheet
                                            var workbook = xlsx.xl['workbook.xml'];
                                            $('sheets sheet:first', workbook).attr('name', 'Data Transaksi');
                                        }
                                    },
                                    'csv',
                                    'pdf',
                                    'print'
                                ]
                            }
                        ]
                    }
                },
                "pageLength": 25,
                "data": response['data'],
                "columns": baseColumns,
                "order": [[0, 'asc']],
                "paging": true,
                "ordering": true,
                "info": false,
                "filter": true,
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

        // Date picker with onChange handler
        var a = $("#kt_datepicker_1").flatpickr({
            "setDate": new Date(),
            "autoclose": true,
            "onChange": function (selectedDates, dateStr, instance) {
                console.log(dateStr);
                updateTable();
                updateStats();
            }
        });

        // Filter Pasar onChange handler
        $("#filter_pasar").on('change', function() {
            console.log('Filter Pasar changed:', $(this).val());
            updateTable();
            updateStats();
        });

        // Filter Petugas onChange handler
        $("#filter_petugas").on('change', function() {
            console.log('Filter Petugas changed:', $(this).val());
            updateTable();
            updateStats();
        });

        // Filter Distrik onChange handler
        $("#filter_distrik").on('change', function() {
            console.log('Filter Distrik changed:', $(this).val());
            updateTable();
            updateStats();
        });

        // Hapus Transaksi handler (using event delegation)
        $(document).on('click', '.btn-hapus-transaksi', function() {
            var transactionId = $(this).data('id');
            var pedagangName = $(this).data('pedagang');

            Swal.fire({
                title: 'Hapus Transaksi?',
                html: 'Apakah Anda yakin ingin menghapus transaksi ini?<br><br>' +
                      '<strong>ID Transaksi:</strong> ' + transactionId + '<br>' +
                      '<strong>Pedagang:</strong> ' + pedagangName + '<br><br>' +
                      '<span class="text-danger">Tagihan terkait akan direset ke status belum dibayar.</span>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Call API to delete transaction
                    $.ajax({
                        url: "{{ env('APP_URL') }}/hapus_transaksi",
                        method: "POST",
                        timeout: 0,
                        data: {
                            transaction_id: transactionId,
                            deleted_by: '{{ session('nama') }}'
                        }
                    }).done(function(response) {
                        if (response.status === 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Refresh table and stats
                            updateTable();
                            updateStats();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message
                            });
                        }
                    }).fail(function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghapus transaksi: ' + error
                        });
                    });
                }
            });
        });

    }, { once: true })

</script>
@endscript