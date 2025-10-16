<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- plugins:css -->
    <link rel="stylesheet" href="../vendors/feather/feather.css">
    <link rel="stylesheet" href="../vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- <link rel="stylesheet" href="../vendors/datatables.net-bs4/dataTables.bootstrap4.css"> -->
    <link rel="stylesheet" href="../vendors/ti-icons/css/themify-icons.css">
    <!-- <link rel="stylesheet" type="text/css" href="../js/select.dataTables.min.css"> -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../css/vertical-layout-light/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


    <link
        href="https://cdn.datatables.net/v/bs4/jszip-3.10.1/dt-2.3.1/b-3.2.3/b-colvis-3.2.3/b-html5-3.2.3/b-print-3.2.3/datatables.min.css"
        rel="stylesheet" integrity="sha384-FKuKgb/f8yVr2vetEBbYdj38X8vPFKR2ZyVSQWuU/7nqr33R0Nag/5eXu2KBAw/I"
        crossorigin="anonymous">


    <title>Si Pintar Online</title>
    @livewireStyles
</head>

<body>
    {{ $slot }}

    @livewireScripts


    <script src="vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="vendors/chart.js/Chart.min.js"></script>
    <script src="vendors/datatables.net/jquery.dataTables.js"></script>
    <!-- <script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script> -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"
        integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"
        integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n"
        crossorigin="anonymous"></script>
    <script
        src="https://cdn.datatables.net/v/bs4/jszip-3.10.1/dt-2.3.1/b-3.2.3/b-colvis-3.2.3/b-html5-3.2.3/b-print-3.2.3/datatables.min.js"
        integrity="sha384-/SOLy+FD2Z49TETbSLP4Y+rIOy284ull+bYtRSDoIi6J9Q4JwSiSKq1j7xXPh4jS"
        crossorigin="anonymous"></script>


    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="js/dashboard.js"></script>
    <script src="js/pedagang.js"></script>
    <script src="js/Chart.roundedBarCharts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>