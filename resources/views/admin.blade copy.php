<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 2 asdasdadasdasdadasdadasda</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <?php /*<link rel="stylesheet" href="{{ asset('backend') }}/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('backend') }}/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('backend') }}/bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('backend') }}/bower_components/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('backend') }}/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="{{ asset('backend') }}/bower_components/bootstrap-daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="{{ asset('backend') }}/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="{{ asset('backend') }}/plugins/timepicker/bootstrap-timepicker.min.css">

    <link rel="stylesheet" href="{{ asset('backend') }}/dist/css/skins/skin-blue.min.css">
    <link rel="stylesheet" href="{{ asset('app') }}/styles/angular-toastr.css"> */ ?>
    <link rel="stylesheet" href="{{ asset('app') }}/styles/compressed_css.css">
    <link rel="stylesheet" href="{{ asset('app') }}/styles/custom.css">


        <script>
            var site_url = "{{ url('/') }}";
            var template_url = "{{ url('/app/') }}";
            var admin_url = "{{ url('/admin/') }}";
            var CSRF_TOKEN = "{{ csrf_token() }}";
        </script>

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="hold-transition skin-blue sidebar-mini sidebar-collapse" ng-app="myApp">

    <ui-view></ui-view>

    <!-- jQuery 3 -->
    <?php /*<script src="{{ asset('backend') }}/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('backend') }}/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- SlimScroll -->
    {{-- <script src="{{ asset('backend') }}/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script> --}}
    <script src="{{ asset('backend') }}//bower_components/select2/dist/js/select2.full.min.js"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('backend') }}/dist/js/adminlte.min.js"></script>
    <script src="{{ asset('app') }}/scripts/jquery.validate.min.js"></script>
    <script src="{{ asset('backend') }}/bower_components/moment/min/moment.min.js"></script>
<script src="{{ asset('backend') }}/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- bootstrap datepicker -->
<script src="{{ asset('backend') }}/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="{{ asset('backend') }}/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script src="{{ asset('app') }}/scripts/additional-methods.min.js"></script>
<script src="{{ asset('backend') }}/dist/js/demo.js"></script> */ ?>

    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('app') }}/scripts/compressed.js"></script>
    <script src="{{ asset('backend') }}/bower_components/ckeditor/ckeditor.js"></script>
    <?php /* <script src="{{ asset('app') }}/scripts/angular.min.js"></script>
    <script src="{{ asset('app') }}/scripts/angular-cookies.min.js"></script>
    <script src="{{ asset('app') }}/scripts/angular-toastr.tpls.js"></script>
    <script src="{{ asset('app') }}/scripts/angular-sanitize.js"></script>
    <script src="{{ asset('app') }}/scripts/angular-animate.js"></script>
    <script src="{{ asset('app') }}/scripts/angular-ui-router.js"></script>
    <script src="{{ asset('app') }}/scripts/ui-bootstrap-tpls-2.5.0.min.js"></script> */ ?>
    <script src="{{ asset('app') }}/scripts/compressed_angular.js"></script>
    <script src="{{ asset('app') }}/scripts/custom.js"></script>
    <script>
        var modulearr = {!! $mlist !!};
    </script>
    <script src="{{ asset('app') }}/scripts/admin.js"></script>
    <script src="{{ asset('app') }}/scripts/directive.js"></script>
    <script src="{{ asset('app') }}/scripts/services.js"></script>
    {{-- <script src="{{ asset('app') }}/scripts/categorycontroller.js"></script> --}}
    <script src="{{ asset('app') }}/scripts/BaseController.js"></script>

</body>

</html>
