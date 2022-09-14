<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 2 </title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <?php /*<link rel="stylesheet" href="{{ asset('/public/backend') }}/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->

    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('/public/backend') }}/bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('/public/backend') }}/bower_components/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="{{ asset('/public/backend') }}/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="{{ asset('/public/backend') }}/bower_components/bootstrap-daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="{{ asset('/public/backend') }}/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="{{ asset('/public/backend') }}/plugins/timepicker/bootstrap-timepicker.min.css">

    <link rel="stylesheet" href="{{ asset('/public/backend') }}/dist/css/skins/skin-blue.min.css">
     */
    ?>
    {{-- <link rel="stylesheet" href="{{ asset('/public/app') }}/styles/compressed_css.css">
    --}}
    <link href="{{ asset('/public/backend') }}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/public/backend') }}/assets/css/app.min.css" rel="stylesheet" type="text/css" id="light-style" />
    <link href="{{ asset('/public/backend') }}/assets/css/app-dark.min.css" rel="stylesheet" type="text/css" id="dark-style" />
    <link rel="stylesheet" href="{{ asset('/public/backend') }}/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('/public/app') }}/styles/angular-toastr.css">
    <link rel="stylesheet" href="{{ asset('/public/owlcarousel2') }}/dist/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="{{ asset('/public/owlcarousel2') }}/dist/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="{{ asset('/public/app') }}/styles/custom.css">
    <link rel="stylesheet" href="{{ asset('/public/css/custom.css') }}">
    <style>
        /* @font-face {
            font-family: 'YourFontName';

            src: url('/public/fonts/pointfree.ttf');

        } */
       
        .cursor-pointer {
            cursor: pointer;
        }
        .card-body {
            overflow-x: auto;
        }

        @font-face {
            font-family: 'Roboto Black';
            font-style: normal;
            font-weight: 700;
            src: local('Roboto Black'), local('RobotoBk-Bold'),
                url(https://allfont.net/cache/fonts/roboto-black_9d5456046bfe9a00b0b9325cda8c55f3.woff) format('woff'),
                url(https://allfont.net/cache/fonts/roboto-black_9d5456046bfe9a00b0b9325cda8c55f3.ttf) format('truetype');
        }
    </style>

    <script>
        var site_url = "{{ url('/') }}";
        var template_url = "{{ url('/public/app/') }}";
        var admin_url = "{{ url('/admin/') }}";
        var CSRF_TOKEN = "{{ csrf_token() }}";
    </script>

    

    <style>
        

    </style>
</head>

<body ng-app="myApp" data-leftbar-theme="dark">

    <ui-view></ui-view>

    <!-- jQuery 3 -->
    <?php /*<script src="{{ asset('/public/backend') }}/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('/public/backend') }}/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- SlimScroll -->
    {{-- <script src="{{ asset('/public/backend') }}/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script> --}}
    <script src="{{ asset('/public/backend') }}//bower_components/select2/dist/js/select2.full.min.js"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('/public/backend') }}/dist/js/adminlte.min.js"></script>
    <script src="{{ asset('/public/app') }}/scripts/jquery.validate.min.js"></script>
    <script src="{{ asset('/public/backend') }}/bower_components/moment/min/moment.min.js"></script>
<script src="{{ asset('/public/backend') }}/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- bootstrap datepicker -->
<script src="{{ asset('/public/backend') }}/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="{{ asset('/public/backend') }}/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script src="{{ asset('/public/app') }}/scripts/additional-methods.min.js"></script>
<script src="{{ asset('/public/backend') }}/dist/js/demo.js"></script> */ ?>

    <!-- AdminLTE for demo purposes -->
    {{-- <script src="{{ asset('/public/app') }}/scripts/compressed.js"></script> --}}
    <script src="{{ asset('/public/backend') }}/assets/js/vendor.min.js"></script>
    <script src="{{ asset('/public/backend') }}/assets/js/app.min.js"></script>
    <script src="{{ asset('/public/backend') }}/assets/js/jquery.validate.min.js"></script>
    <script src="{{ asset('/public/backend') }}/assets/js/underscore-min.js"></script>
    <script src="{{ asset('/public/backend') }}/assets/js/additional-methods.min.js"></script>
    <!-- <script src="{{ asset('/public/backend') }}/bower_components/ckeditor/ckeditor.js"></script> -->
    <script src="{{ asset('/public/') }}/ck5/build/ckeditor.js"></script>
    <script src="{{url('public/js/Chart.min.js')}}"></script>
    <script src="{{url('public/js/utils.js')}}"></script>

    <script src="{{ asset('/public/') }}/owlcarousel2/dist/owl.carousel.min.js"></script>


    <?php /* <script src="{{ asset('/public/app') }}/scripts/angular.min.js"></script>
    <script src="{{ asset('/public/app') }}/scripts/angular-cookies.min.js"></script>
    <script src="{{ asset('/public/app') }}/scripts/angular-toastr.tpls.js"></script>
    <script src="{{ asset('/public/app') }}/scripts/angular-sanitize.js"></script>
    <script src="{{ asset('/public/app') }}/scripts/angular-animate.js"></script>
    <script src="{{ asset('/public/app') }}/scripts/angular-ui-router.js"></script>
    <script src="{{ asset('/public/app') }}/scripts/ui-bootstrap-tpls-2.5.0.min.js"></script> */ ?>
    <script src="{{ asset('/public/app') }}/scripts/compressed_angular.js"></script>
    <script src="{{ asset('/public/app') }}/scripts/custom.js"></script>
    <script>
        var modulearr = <?php echo $mlist; ?>;
    </script>
    <script src="{{ asset('/public/app') }}/scripts/admin.js?ver={{rand()}}"></script>
    <script src="{{ asset('/public/app') }}/scripts/directive.js?ver={{rand()}}"></script>
    <script src="{{ asset('/public/app') }}/scripts/services.js?ver={{rand()}}"></script>
    {{-- <script src="{{ asset('/public/app') }}/scripts/categorycontroller.js?ver={{rand()}}"></script> --}}
    <script src="{{ asset('/public/app') }}/scripts/BaseController.js?ver={{rand()}}"></script>
    <script src="{{ asset('/public/app') }}/scripts/controllers/lessonController.js?ver={{rand()}}"></script>
    <script src="{{ asset('/public/app') }}/scripts/controllers/fragmentController.js?ver={{rand()}}"></script>
    <script src="{{ asset('/public/app') }}/scripts/scorecontroller.js?ver={{rand()}}"></script>
    <script src="{{ asset('/public/app') }}/scripts/controllers/usersformController.js?ver={{rand()}}"></script>
    <script src="{{ asset('/public/app') }}/scripts/controllers/audioRecordingStudentController.js?ver={{rand()}}"></script>
    <script src="{{ asset('/public/app') }}/scripts/controllers/speakinglabController.js?ver={{rand()}}"></script>
    <script src="{{ asset('/public/app') }}/scripts/controllers/achievementsController.js?ver={{rand()}}"></script>


    <!-- <script>
        $(document).on('click', '.button-menu-mobile', function(e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-enable');
        });

        $(document).on('click', '.metismenu a:not(.red)', function(e) {
            e.preventDefault();
            var sib = $(this).next('.side-nav-second-level');
            console.log(sib.length);
            if (sib.length == 0) {
                $('body').removeClass('sidebar-enable');
            }

        });
    </script> -->


</body>

</html>