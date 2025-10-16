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
                <h6 class="font-weight-normal mb-0">All systems are running smoothly! You have <span
                    class="text-primary">3 unread alerts!</span></h6>
              </div>
              <div class="col-12 col-xl-4">
                <div class="justify-content-end d-flex">
                  <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                    <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button" id="dropdownMenuDate2"
                      data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                      <i class="mdi mdi-calendar"></i> Today (10 Jan 2021)
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                      <a class="dropdown-item" href="#">January - March</a>
                      <a class="dropdown-item" href="#">March - June</a>
                      <a class="dropdown-item" href="#">June - August</a>
                      <a class="dropdown-item" href="#">August - November</a>
                    </div>
                  </div>
                </div>
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
                    <p class="mb-4">Total Transaksi Hari Ini</p>
                    <p id="jumlah_transaksi_hari_ini" class="fs-30 mb-2">0</p>
                    <p>{{session('nama_pasar')}}</p>
                    <!-- <p>10.00% (30 days)</p> -->
                  </div>
                </div>
              </div>
              <div class="col-md-6 mb-4 stretch-card transparent">
                <div class="card card-dark-blue">
                  <div class="card-body">
                    <p class="mb-4">Total Transaksi 30 Hari Terakhir</p>
                    <p id="jumlah_transaksi_30_hari_terakhir" class="fs-30 mb-2">0</p>
                    <p>{{session('nama_pasar')}}</p>
                    <!-- <p>22.00% (30 days)</p> -->
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                <div class="card card-light-blue">
                  <div class="card-body">
                    <p class="mb-4">Total Nominal Hari Ini</p>
                    <p id="total_nominal_hari_ini" class="fs-30 mb-2">Rp. </p>
                    <p>{{session('nama_pasar')}}</p>
                    <!-- <p>2.00% (30 days)</p> -->
                  </div>
                </div>
              </div>
              <div class="col-md-6 stretch-card transparent">
                <div class="card card-light-danger">
                  <div class="card-body">
                    <p class="mb-4">Total Nominal 30 Hari Terakhir</p>
                    <p id="total_nominal_30_hari_terakhir" class="fs-30 mb-2">Rp. 0</p>
                    <p>{{session('nama_pasar')}}</p>
                    <!-- <p>0.22% (30 days)</p> -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class=" grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h3 class="font-weight-bold">Jumlah Transaksi</h3>
              <p class="card-description">
                Semua transaksi di {{session('nama_pasar')}} selama 7 hari terakhir.
              </p>
              <canvas id="areaChart"></canvas>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 grid-margin stretch-card">
            <div class="card position-relative">
              <div class="card-body">
                <div class="carousel-item active">
                  <div class="row">
                    <div class="col-md-12 col-xl-3 d-flex flex-column justify-content-start">
                      <div class="ml-xl-4 mt-3">
                        <p class="card-title">Detailed Reports</p>
                        <h1 class="text-primary">Rp. {{ number_format($data->total_nominal_30_hari_terakhir)}}</h1>
                        <h3 class="font-weight-500 mb-xl-4 text-primary">{{session('nama_pasar')}}</h3>
                        <p class="mb-2 mb-xl-0">The total number of transaction in Pasar Margahayu within last 30
                          days. It is the period
                          time a user is actively engaged with your website, page or app, etc</p>
                      </div>
                    </div>
                    <div class="col-md-12 col-xl-9">
                      <div class="row">
                        <div class="col-md-6 border-right">
                          <div class="table-responsive mb-3 mb-md-0 mt-3">
                            <table class="table table-borderless report-table">
                              @foreach($data->users_stat as $user)
                                <tr>
                                  <td class="text-muted">{{ $user->nama_petugas }}</td>
                                  <td class="w-100 px-0">
                                    <div class="progress progress-md mx-4">
                                      @php
                                        $max = $data->jumlah_transaksi_30_hari_terakhir;
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

    var usersStat = @json($data->users_stat);
    var labels = usersStat.map(function (item) { return item.nama_petugas; });
    var data = usersStat.map(function (item) { return item.jumlah_transaksi; });

    if ($("#north-america-chart").length) {
      // Fungsi untuk menghasilkan warna random dalam format hex

      var colors = @json($colors);

      var areaData = {
        labels: labels,
        datasets: [{
          data: data,
          // backgroundColor: [
          //   'rgba(255, 99, 132, 0.2)',
          //   'rgba(54, 162, 235, 0.2)',
          // ],
          backgroundColor: colors,
          borderColor: "rgba(0,0,0,0)"
        }
        ]
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
      }
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

          var text = {{ $data->jumlah_transaksi_30_hari_terakhir }},
            textX = Math.round((width - ctx.measureText(text).width) / 2),
            textY = height / 2;

          ctx.fillText(text, textX, textY);
          ctx.save();
        }
      }
      var northAmericaChartCanvas = $("#north-america-chart").get(0).getContext("2d");
      var northAmericaChart = new Chart(northAmericaChartCanvas, {
        type: 'doughnut',
        data: areaData,
        options: areaOptions,
        plugins: northAmericaChartPlugins
      });
      document.getElementById('north-america-legend').innerHTML = northAmericaChart.generateLegend();
    }


    var settings = {
      "url": "{{ env('APP_URL') }}/home",
      "method": "POST",
      "timeout": 0,
      "data": {
        "tanggal": "{{ $date }}",
        "nama_pasar": '{{ session('nama_pasar') }}'
      }
    };

    console.log("{{ $date }}");

    $.ajax(settings).done(function (response) {

      console.log(response['data']);
      var data = response['data'];

      // Fungsi untuk menambahkan number_format (format ribuan) pada angka
      function number_format(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      }

      $('#jumlah_transaksi_30_hari_terakhir').text(number_format(data['jumlah_transaksi_30_hari_terakhir']));
      $('#total_nominal_30_hari_terakhir').text('Rp. ' + number_format(data['total_nominal_30_hari_terakhir']));
      $('#jumlah_transaksi_hari_ini').text(number_format(data['jumlah_transaksi_hari_ini']));
      $('#total_nominal_hari_ini').text('Rp. ' + number_format(data['total_nominal_hari_ini']));
    });

    var areaData = {
      labels: ["2013", "2014", "2015", "2016", "2017"],
      datasets: [{
        label: '# of Votes',
        data: [12, 19, 3, 5, 2, 3],
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 159, 64, 0.2)'
        ],
        borderColor: [
          'rgba(255,99,132,1)',
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 159, 64, 1)'
        ],
        borderWidth: 1,
        fill: true, // 3: no fill
      }]
    };

    var areaOptions = {
      plugins: {
        filler: {
          propagate: true
        }
      }
    };


    var settings = {
      "url": "{{ env('APP_URL') }}/revenue_chart",
      "method": "POST",
      "timeout": 0,
      "data": {
        "nama_pasar": '{{ session('nama_pasar') }}'
      }
    };

    $.ajax(settings).done(function (response) {
      var data = response['data'];
      // console.log(data);

      if ($("#areaChart").length) {
        var areaChartCanvas = $("#areaChart").get(0).getContext("2d");
        var areaChart = new Chart(areaChartCanvas, {
          type: 'line',
          data: {
            labels: data['tanggal'],
            datasets: [
              {
                label: '{{session('nama_pasar')}}',
                data: data['nominal'],
                borderColor: '#FF6384',
                backgroundColor: '#FFB1C1',
              }
            ]
          },
          options: areaOptions
        });
      }

    });


    //untuk keperluan cuaca OpenWeather
    const apiKey = "112c6ca6e7bf44733969c4d576284272";
    const city = "Pasar Margahayu";

    const lat = -6.974991181293871;
    const lon = 107.56163059688262;

    fetch(`https://api.openweathermap.org/data/2.5/weather?lat=-6.974991181293871&lon=107.56163059688262&appid=c45e03b5bb905a83ead1f79a80ec6c67&lang=id&units=metric`)
      .then(response => response.json())
      .then(data => {
        console.log(data);
        const suhu = data.main.temp;
        const cuaca = data.weather[0].description;
        document.getElementById('temp').innerHTML = `<i class="icon-sun mr-2"></i>${Math.round(suhu)}<sup>C</sup>`;
        document.getElementById('weather').innerText = cuaca.charAt(0).toUpperCase() + cuaca.slice(1);
        document.getElementById('location').innerText = data.name;
        // console.log(cuaca);
      });
  });




</script>
@endscript