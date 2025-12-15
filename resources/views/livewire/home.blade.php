<div class="container-scroller">
  <!-- partial:partials/_navbar.html -->
  <livewire:navBar />
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
        <div class="row">
          <div class="col-md-12 grid-margin">
            <div class="row">
              <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <h3 class="font-weight-bold">Welcome {{ session('nama') }}</h3>
                <h6 class="font-weight-normal mb-0">
                  @if(session('role') === 'superadmin')
                    Dashboard Superadmin - Semua Pasar
                  @else
                    Dashboard {{ session('nama_pasar') }}
                  @endif
                </h6>
                <hr>
                <p><b>{{ date('l, d-m-Y') }}</b></p>
              </div>
              <div class="col-12 col-xl-4">
                <div class="justify-content-end d-flex">
                  <button wire:click="applyFilters" class="btn btn-primary">
                    <i class="mdi mdi-refresh"></i> Refresh Data
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter Section -->
        <div class="row mb-3">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Filter Dashboard</h4>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Tanggal Mulai</label>
                      <input type="date" class="form-control" wire:model="start_date">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Tanggal Akhir</label>
                      <input type="date" class="form-control" wire:model="end_date">
                    </div>
                  </div>
                  @if(session('role') === 'superadmin')
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Pilih Pasar</label>
                        <select class="form-control" wire:model="selected_pasar">
                          <option value="">Semua Pasar</option>
                          @foreach($pasar_list as $pasar)
                            <option value="{{ $pasar->nama }}">{{ $pasar->nama }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  @endif
                </div>
                <button wire:click="applyFilters" class="btn btn-primary">
                  <i class="mdi mdi-filter"></i> Terapkan Filter
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 grid-margin stretch-card">
            <div class="card tale-bg">
              <div class="card-people mt-auto">
                <img src="images/dashboard/people.svg" alt="people">
                <div class="weather-info">
                  <div class="d-flex">
                    <div>
                      <h2 id="temp" class="mb-0 font-weight-normal"><i class="icon-sun mr-2"></i>31<sup>C</sup></h2>
                    </div>
                    <div class="ml-2">
                      <h4 id="weather" class="location font-weight-normal">-</h4>
                      <h6 id="location" class="font-weight-normal">-</h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 grid-margin transparent">
            <div class="row">
              <div class="col-md-6 mb-4 stretch-card transparent">
                <div class="card card-tale">
                  <div class="card-body">
                    <p class="mb-4">Total Transaksi Periode Dipilih</p>
                    <p id="jumlah_transaksi_hari_ini" class="fs-30 mb-2">0</p>
                    <p>{{ $selected_pasar ?? session('nama_pasar') }}</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mb-4 stretch-card transparent">
                <div class="card card-light-danger">
                  <div class="card-body">
                    <p class="mb-4">Total Transaksi Hari Ini</p>
                    <p id="jumlah_transaksi_hari_ini_card" class="fs-30 mb-2">0</p>
                    <p>{{ $selected_pasar ?? session('nama_pasar') }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                <div class="card card-tale">
                  <div class="card-body">
                    <p class="mb-4">Total Nominal Periode Dipilih</p>
                    <p id="total_nominal_hari_ini" class="fs-30 mb-2">Rp. </p>
                    <p>{{ $selected_pasar ?? session('nama_pasar') }}</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6 stretch-card transparent">
                <div class="card card-light-danger">
                  <div class="card-body">
                    <p class="mb-4">Total Nominal Hari Ini</p>
                    <p id="total_nominal_hari_ini_card" class="fs-30 mb-2">Rp. 0</p>
                    <p>{{ $selected_pasar ?? session('nama_pasar') }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Revenue Chart -->
        <div class="grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h3 class="font-weight-bold">Grafik Pendapatan Per Pasar</h3>
              <p class="card-description">
                @if(session('role') === 'superadmin')
                  Perbandingan pendapatan harian antar pasar dalam periode yang dipilih. Setiap line mewakili satu pasar.
                @else
                  Pendapatan harian {{ session('nama_pasar') }} dalam periode yang dipilih dengan perbandingan periode
                  sebelumnya (garis putus-putus)
                @endif
              </p>
              <canvas id="areaChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Payment Methods Chart -->
        <div class="row">
          <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Metode Pembayaran</h4>
                <canvas id="paymentMethodsChart"></canvas>
              </div>
            </div>
          </div>

          <!-- Tagihan Status Chart -->
          <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Status Tagihan Hari Ini</h4>
                <canvas id="tagihanStatusChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Markets Chart (Superadmin Only) -->
        @if(session('role') === 'superadmin')
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Perbandingan Antar Pasar</h4>
                  <canvas id="marketsChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        @endif

        <!-- User Statistics -->
        <div class="row">
          <div class="col-md-12 grid-margin stretch-card">
            <div class="card position-relative">
              <div class="card-body">
                <div class="carousel-item active">
                  <div class="row">
                    <div class="col-md-12 col-xl-3 d-flex flex-column justify-content-start">
                      <div class="ml-xl-4 mt-3">
                        <p class="card-title">Detailed Reports</p>
                        <h1 class="text-primary">Rp. {{ number_format($data->total_nominal_30_hari_terakhir ?? 0)}}</h1>
                        <h3 class="font-weight-500 mb-xl-4 text-primary">{{ $selected_pasar ?? session('nama_pasar') }}
                        </h3>
                        <p class="mb-2 mb-xl-0">Total transaksi dalam 30 hari terakhir berdasarkan petugas</p>
                      </div>
                    </div>
                    <div class="col-md-12 col-xl-9">
                      <div class="row">
                        <div class="col-md-6 border-right">
                          <div class="table-responsive mb-3 mb-md-0 mt-3">
                            <table class="table table-borderless report-table">
                              @foreach(($data->users_stat ?? []) as $user)
                                <tr>
                                  <td class="text-muted">{{ $user->nama_petugas }}</td>
                                  <td class="w-100 px-0">
                                    <div class="progress progress-md mx-4">
                                      @php
                                        $max = $data->jumlah_transaksi_30_hari_terakhir ?? 1;
                                        $percent = $max > 0 ? ($user->jumlah_transaksi / $max) * 100 : 0;
                                      @endphp
                                      <div class="progress-bar" role="progressbar"
                                        style="width: {{ $percent }}%; background-color: {{ $colors[$loop->index] ?? '#007bff' }};"
                                        aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                  </td>
                                  <td>
                                    <h5 class="font-weight-bold mb-0">{{ $user->jumlah_transaksi }}</h5>
                                  </td>
                                </tr>
                              @endforeach
                            </table>
                          </div>
                        </div>
                        <div class="col-md-6 mt-3">
                          <canvas id="north-america-chart"></canvas>
                          <div id="north-america-legend"></div>
                        </div>
                      </div>
                    </div>
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
          <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2021. Premium <a
              href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a> from BootstrapDash. All
            rights reserved.</span>
          <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i
              class="ti-heart text-danger ml-1"></i></span>
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
    initializeCharts();
  });

  $wire.on('dataLoaded', () => {
    console.log('Data loaded, reinitializing charts');
    initializeCharts();
  });

  function initializeCharts() {
    // Helper function to get correct nama_pasar based on role and selected_pasar
    function getNamaPasar() {
      var selectedPasar = @this.get('selected_pasar');
      var userRole = '{{ session('role') }}';

      // If selected_pasar is empty/null
      if (selectedPasar === '' || selectedPasar === null || selectedPasar === undefined) {
        // Superadmin with no selection = All Markets (null)
        if (userRole === 'superadmin') {
          return null;
        }
        // Non-superadmin with no selection = their assigned market
        return '{{ session('nama_pasar') }}';
      }

      // If selected_pasar has a value, use it
      return selectedPasar;
    }

    // Weather API - menggunakan koordinat dari tabel pasar
    var latitude = @this.get('latitude') || {{ $latitude ?? -6.974991181293871 }};
    var longitude = @this.get('longitude') || {{ $longitude ?? 107.56163059688262 }};

    console.log('Loading weather for coordinates:', latitude, longitude);

    fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${latitude}&lon=${longitude}&appid=c45e03b5bb905a83ead1f79a80ec6c67&lang=id&units=metric`)
      .then(response => response.json())
      .then(data => {
        const suhu = data.main.temp;
        const cuaca = data.weather[0].description;
        document.getElementById('temp').innerHTML = `<i class="icon-sun mr-2"></i>${Math.round(suhu)}<sup>C</sup>`;
        document.getElementById('weather').innerText = cuaca.charAt(0).toUpperCase() + cuaca.slice(1);
        document.getElementById('location').innerText = data.name;
      })
      .catch(error => {
        console.error('Error fetching weather data:', error);
        document.getElementById('temp').innerHTML = `<i class="icon-sun mr-2"></i>--<sup>C</sup>`;
        document.getElementById('weather').innerText = 'Tidak tersedia';
        document.getElementById('location').innerText = @this.get('selected_pasar') || '{{ session("nama_pasar") }}';
      });

    // Chart initialization code below
    var usersStat = @json($data->users_stat ?? []);
    var colors = @json($colors ?? []);

    // Safety check: ensure usersStat is an array
    if (!Array.isArray(usersStat)) {
      usersStat = [];
    }

    var labels = usersStat.map(function (item) { return item.nama_petugas; });
    var data = usersStat.map(function (item) { return item.jumlah_transaksi; });

    // Doughnut Chart - User Statistics
    if ($("#north-america-chart").length && usersStat.length > 0) {

      var areaData = {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: colors,
          borderColor: "rgba(0,0,0,0)"
        }]
      };

      var areaOptions = {
        responsive: true,
        maintainAspectRatio: true,
        segmentShowStroke: false,
        cutoutPercentage: 78,
        elements: {
          arc: {
            borderWidth: 4
          }
        },
        legend: {
          display: false
        },
        tooltips: {
          enabled: true
        },
        legendCallback: function (chart) {
          var text = [];
          text.push('<div class="report-chart">');
          for (var i = 0; i < labels.length; i++) {
            text.push('<div class="d-flex justify-content-between mx-4 mx-xl-5 mt-3"><div class="d-flex align-items-center"><div class="mr-3" style="width:20px; height:20px; border-radius: 50%; background-color: ' + chart.data.datasets[0].backgroundColor[i] + '"></div><p class="mb-0">' + labels[i] + '</p></div>');
            text.push('<p class="mb-0">' + data[i] + '</p>');
            text.push('</div>');
          }
          text.push('</div>');
          return text.join("");
        },
      };

      var northAmericaChartPlugins = {
        beforeDraw: function (chart) {
          var width = chart.chart.width,
            height = chart.chart.height,
            ctx = chart.chart.ctx;

          ctx.restore();
          var fontSize = 3.125;
          ctx.font = "500 " + fontSize + "em sans-serif";
          ctx.textBaseline = "middle";
          ctx.fillStyle = "#13381B";

          var text = {{ $data->jumlah_transaksi_30_hari_terakhir ?? 0 }},
            textX = Math.round((width - ctx.measureText(text).width) / 2),
            textY = height / 2;

          ctx.fillText(text, textX, textY);
          ctx.save();
        }
      };

      // Destroy existing chart if exists
      if (window.northAmericaChart && typeof window.northAmericaChart.destroy === 'function') {
        try {
          window.northAmericaChart.destroy();
        } catch (e) {
          console.warn('Failed to destroy northAmericaChart:', e);
        }
      }

      var northAmericaChartCanvas = $("#north-america-chart").get(0).getContext("2d");
      window.northAmericaChart = new Chart(northAmericaChartCanvas, {
        type: 'doughnut',
        data: areaData,
        options: areaOptions,
        plugins: northAmericaChartPlugins
      });
      document.getElementById('north-america-legend').innerHTML = window.northAmericaChart.generateLegend();
    }

    var data_home = {
      "tanggal": "{{ $date }}",
      "nama_pasar": getNamaPasar(),
      "start_date": @this.get('start_date'),
      "end_date": @this.get('end_date')
    };

    // Load main statistics
    var settings = {
      "url": "{{ env('APP_URL') }}/home",
      "method": "POST",
      "timeout": 0,
      "data": data_home
    };

    $.ajax(settings).done(function (response) {
      var data = response['data'];

      function number_format(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      }

      // Card periode dipilih
      $('#jumlah_transaksi_hari_ini').text(number_format(data['jumlah_transaksi_hari_ini']));
      $('#total_nominal_hari_ini').text('Rp. ' + number_format(data['total_nominal_hari_ini']));
    });

    // Load data hari ini (tanggal sekarang)
    var settingsToday = {
      "url": "{{ env('APP_URL') }}/dashboard",
      "method": "POST",
      "timeout": 0,
      "data": {
        "tanggal": "{{ date('Y-m-d') }}",
        "nama_pasar": getNamaPasar(),
        "role": '{{ session('role') }}'
      }
    };

    $.ajax(settingsToday).done(function (response) {
      var data = response['data'];

      function number_format(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      }

      // Card hari ini
      $('#jumlah_transaksi_hari_ini_card').text(number_format(data['jumlah_transaksi'] || 0));
      $('#total_nominal_hari_ini_card').text('Rp. ' + number_format(data['total_nominal'] || 0));
    });

    $data_revenue = {
      "role": '{{ session('role') }}',
      "nama_pasar": getNamaPasar(),
      "start_date": @this.get('start_date'),
      "end_date": @this.get('end_date')
    }

    // Revenue Chart (Line Chart)
    var settingsRevenue = {
      "url": "{{ env('APP_URL') }}/revenue_chart",
      "method": "POST",
      "timeout": 0,
      "data": $data_revenue
    };

    console.log('Loading revenue chart with settings:', settingsRevenue);

    $.ajax(settingsRevenue).done(function (response) {
      console.log('Revenue chart response:', response);
      try {
        var data = response['data'];

        if (!data || !data.tanggal || !data.datasets) {
          console.warn('Revenue chart: Invalid data structure');
          return;
        }

        var canvasElement = $("#areaChart");
        if (!canvasElement.length) {
          console.warn('Revenue chart: Canvas element not found');
          return;
        }

        // Destroy existing chart if exists
        if (window.areaChart && typeof window.areaChart.destroy === 'function') {
          try {
            window.areaChart.destroy();
          } catch (e) {
            console.warn('Failed to destroy areaChart:', e);
          }
        }

        // Predefined colors untuk setiap pasar
        var pasarColors = [
          { border: '#FF6384', background: 'rgba(255, 99, 132, 0.2)' },  // Merah
          { border: '#36A2EB', background: 'rgba(54, 162, 235, 0.2)' },  // Biru
          { border: '#FFCE56', background: 'rgba(255, 206, 86, 0.2)' },  // Kuning
          { border: '#4BC0C0', background: 'rgba(75, 192, 192, 0.2)' },  // Hijau/Cyan
          { border: '#9966FF', background: 'rgba(153, 102, 255, 0.2)' }, // Ungu
          { border: '#FF9F40', background: 'rgba(255, 159, 64, 0.2)' },  // Orange
          { border: '#FF6384', background: 'rgba(255, 99, 132, 0.2)' },  // Merah (repeat)
          { border: '#C9CBCF', background: 'rgba(201, 203, 207, 0.2)' }  // Abu-abu
        ];

        // Build datasets untuk Chart.js
        var chartDatasets = [];
        data.datasets.forEach(function (dataset, index) {
          var colorIndex = index % pasarColors.length;

          // Check if this is previous period data
          var isPreviousPeriod = dataset.isPreviousPeriod || false;

          chartDatasets.push({
            label: dataset.nama_pasar,
            data: dataset.data,
            borderColor: isPreviousPeriod ? 'rgba(255, 99, 132, 0.5)' : pasarColors[colorIndex].border,
            backgroundColor: isPreviousPeriod ? 'rgba(255, 99, 132, 0.1)' : pasarColors[colorIndex].background,
            borderWidth: 2,
            borderDash: isPreviousPeriod ? [5, 5] : [], // Dashed line for previous period
            fill: true,
            tension: 0.4, // Smooth line
            previousDates: dataset.previousDates || null // Simpan tanggal periode sebelumnya
          });
        });

        var areaChartCanvas = canvasElement.get(0).getContext("2d");
        window.areaChart = new Chart(areaChartCanvas, {
          type: 'line',
          data: {
            labels: data['tanggal'],
            datasets: chartDatasets
          },
          options: {
            responsive: true,
            maintainAspectRatio: true,
            legend: {
              display: true,
              position: 'top'
            },
            tooltips: {
              mode: 'index',
              intersect: false,
              callbacks: {
                label: function (tooltipItem, data) {
                  var datasetIndex = tooltipItem.datasetIndex;
                  var index = tooltipItem.index;
                  var dataset = data.datasets[datasetIndex];
                  var label = dataset.label || '';

                  // Jika dataset memiliki previousDates (periode sebelumnya), tampilkan tanggal itu
                  if (dataset.previousDates && dataset.previousDates[index]) {
                    label += ' [' + dataset.previousDates[index] + ']';
                  } else {
                    // Untuk periode saat ini, tampilkan tanggal dari label
                    label += ' [' + tooltipItem.xLabel + ']';
                  }

                  label += ': Rp ' + tooltipItem.yLabel.toLocaleString('id-ID');

                  // Tambahkan perbandingan persentase jika ini adalah periode saat ini (dataset pertama)
                  if (datasetIndex === 0 && data.datasets.length > 1) {
                    var currentValue = tooltipItem.yLabel;
                    var previousDataset = data.datasets[1];
                    var previousValue = previousDataset.data[index];

                    if (previousValue && previousValue > 0) {
                      var percentageChange = ((currentValue - previousValue) / previousValue) * 100;
                      var arrow = percentageChange >= 0 ? '▲' : '▼';
                      var sign = percentageChange >= 0 ? '+' : '';

                      label += ' (' + arrow + ' ' + sign + percentageChange.toFixed(2) + '%)';
                    } else if (currentValue > 0 && previousValue === 0) {
                      label += ' (▲ Baru)';
                    }
                  }

                  return label;
                }
              }
            },
            scales: {
              yAxes: [{
                ticks: {
                  beginAtZero: true,
                  callback: function (value) {
                    return 'Rp ' + value.toLocaleString('id-ID');
                  }
                }
              }],
              xAxes: [{
                ticks: {
                  autoSkip: true,
                  maxTicksLimit: 10
                }
              }]
            },
            hover: {
              mode: 'nearest',
              intersect: false
            }
          }
        });
      } catch (error) {
        console.error('Error creating revenue chart:', error);
      }
    }).fail(function (xhr, status, error) {
      console.error('Failed to load revenue chart:', error);
    });

    // Payment Methods Chart
    var settingsPayment = {
      "url": "{{ env('APP_URL') }}/payment_methods_chart",
      "method": "POST",
      "timeout": 0,
      "data": {
        "nama_pasar": getNamaPasar(),
        "role": '{{ session('role') }}',
        "start_date": @this.get('start_date'),
        "end_date": @this.get('end_date')
      }
    };

    $.ajax(settingsPayment).done(function (response) {
      try {
        var data = response['data'];

        if (!data || !Array.isArray(data)) {
          console.warn('Payment methods chart: Invalid data structure');
          return;
        }

        var labels = data.map(item => item.metode_pembayaran);
        var values = data.map(item => item.total);

        var canvasElement = $("#paymentMethodsChart");
        if (!canvasElement.length) {
          console.warn('Payment methods chart: Canvas element not found');
          return;
        }

        // Destroy existing chart if exists
        if (window.paymentMethodsChart && typeof window.paymentMethodsChart.destroy === 'function') {
          try {
            window.paymentMethodsChart.destroy();
          } catch (e) {
            console.warn('Failed to destroy paymentMethodsChart:', e);
          }
        }

        var ctx = canvasElement.get(0).getContext("2d");
        window.paymentMethodsChart = new Chart(ctx, {
          type: 'pie',
          data: {
            labels: labels,
            datasets: [{
              data: values,
              backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: true,
            legend: {
              position: 'bottom'
            }
          }
        });
      } catch (error) {
        console.error('Error creating payment methods chart:', error);
      }
    }).fail(function (xhr, status, error) {
      console.error('Failed to load payment methods chart:', error);
    });

    // Tagihan Status Chart
    var settingsTagihan = {
      "url": "{{ env('APP_URL') }}/tagihan_status_chart",
      "method": "POST",
      "timeout": 0,
      "data": {
        "nama_pasar": getNamaPasar(),
        "role": '{{ session('role') }}',
        "tanggal": "{{ $date }}"
      }
    };

    console.log('Loading tagihan status chart with settings:', settingsTagihan);

    $.ajax(settingsTagihan).done(function (response) {
      console.log('Tagihan status chart response:', response);
      try {
        var data = response['data'];

        // Validate data
        if (!data) {
          console.warn('Tagihan status chart: No data received');
          return;
        }

        var canvasElement = $("#tagihanStatusChart");
        if (!canvasElement.length) {
          console.warn('Tagihan status chart: Canvas element not found');
          return;
        }

        // Destroy existing chart if exists
        if (window.tagihanStatusChart && typeof window.tagihanStatusChart.destroy === 'function') {
          try {
            window.tagihanStatusChart.destroy();
          } catch (e) {
            console.warn('Failed to destroy tagihanStatusChart:', e);
          }
        }

        var ctx = canvasElement.get(0).getContext("2d");
        window.tagihanStatusChart = new Chart(ctx, {
          type: 'doughnut',
          data: {
            labels: ['Sudah Dibayar', 'Belum Dibayar'],
            datasets: [{
              data: [data.sudah_dibayar || 0, data.belum_dibayar || 0],
              backgroundColor: ['#4BC0C0', '#FF6384'],
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: true,
            legend: {
              position: 'bottom'
            }
          }
        });
      } catch (error) {
        console.error('Error creating tagihan status chart:', error);
      }
    }).fail(function (xhr, status, error) {
      console.error('Failed to load tagihan status chart:', error, xhr.responseText);
    });

    // Markets Chart (Superadmin Only)
    var userRole = '{{ session('role') }}';
    if (userRole === 'superadmin') {
      var settingsMarkets = {
        "url": "{{ env('APP_URL') }}/markets_chart",
        "method": "POST",
        "timeout": 0,
        "data": {
          "role": userRole,
          "start_date": @this.get('start_date'),
          "end_date": @this.get('end_date')
        }
      };

      $.ajax(settingsMarkets).done(function (response) {
        try {
          var data = response['data'];

          if (!data || !Array.isArray(data)) {
            console.warn('Markets chart: Invalid data structure');
            return;
          }

          var labels = data.map(item => item.nama_pasar);
          var values = data.map(item => item.total_nominal);

          var canvasElement = $("#marketsChart");
          if (!canvasElement.length) {
            console.warn('Markets chart: Canvas element not found');
            return;
          }

          // Destroy existing chart if exists
          if (window.marketsChart && typeof window.marketsChart.destroy === 'function') {
            try {
              window.marketsChart.destroy();
            } catch (e) {
              console.warn('Failed to destroy marketsChart:', e);
            }
          }

          var ctx = canvasElement.get(0).getContext("2d");
          window.marketsChart = new Chart(ctx, {
            type: 'bar',
            data: {
              labels: labels,
              datasets: [{
                label: 'Total Pendapatan (Rp)',
                data: values,
                backgroundColor: '#36A2EB',
              }]
            },
            options: {
              responsive: true,
              maintainAspectRatio: true,
              scales: {
                yAxes: [{
                  ticks: {
                    beginAtZero: true
                  }
                }]
              }
            }
          });
        } catch (error) {
          console.error('Error creating markets chart:', error);
        }
      }).fail(function (xhr, status, error) {
        console.error('Failed to load markets chart:', error);
      });
    }
  }
</script>
@endscript