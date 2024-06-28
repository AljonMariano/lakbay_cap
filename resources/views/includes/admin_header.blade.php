<?php
    $title_page = 'LAKBAY Reservation System';
?>
<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <title><?php echo $title_page; ?></title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/cover/">
    <link rel="icon" type="image/png" href="images/logo-nolabel.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.0/css/dataTables.bootstrap4.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap4.css" />
    <link href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/colreorder/1.5.2/css/colReorder.dataTables.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="css/custom-css.css" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.5.2/js/dataTables.colReorder.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>



    {{-- <script src="https://cdn.datatables.net/2.0.0/js/dataTables.js"></script> --}}


    <style>
        @import url('https://fonts.googleapis.com/css2?family=Khand:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Khand', sans-serif !important;
        }

        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .overlay {
            overflow: hidden;
            position: absolute;
            right: 1rem;
            top: 3rem;
            width: 40px;
        }

        .overlay-text {
            width: 100%;
            position: absolute;
            bottom: 1rem;
        }

        .stat-label {
            font-size: 0.6rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .carousel>.carousel-inner>.carousel-item>img {
            overflow: hidden;
            object-fit: cover;
            object-position: top center;
            border-radius: 0.25rem;
            opacity: 0.2;
            filter: alpha(opacity=40);
            height: 400px;
        }

        .carousel>.carousel-inner>.carousel-item>img:hover {
            opacity: 0.4;
        }

        .small-text {
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 600;
            color: grey;
        }

    </style>
    <link href="{{asset('css/main.css')}}" rel="stylesheet">

</head>
<body class="d-flex">

    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <?php if(empty($_SESSION['loggedIn'])): ?>
        <header class="mb-auto">
            <div>
<!-- <h3 class="float-md-start mb-0">LAKBAY</h3> -->
<a class="navbar-brand float-md-start mb-0 flex-row d-flex" href="{{ url('/index') }}">
    <img src="/images/logo.png" alt="" width="150" height="50" class="d-inline-block align-text-top">
</a>
<nav class="nav nav-masthead justify-content-center float-md-end">
    <a class="nav-link" aria-current="page" href="{{ url('/admin/dashboard') }}">Dashboard</a>
    <a class="nav-link" href="{{ url('/admin/reservations') }}">Reservations</a>
    <a class="nav-link" href="{{ url('/admin/events') }}">Events</a>
    <a class="nav-link" href="{{ url('/admin/vehicles') }}">Vehicles</a>
    <a class="nav-link" href="{{ url('/admin/drivers') }}">Drivers</a>
    <a class="nav-link" href="{{ url('/admin/offices') }}">Offices</a>
    <a class="nav-link" href="{{ url('/admin/requestor/requestors') }}">Requestors</a>
    <a class="nav-link" href="{{ route('profile.show') }}">Profile</a>
    <a class="nav-link" href="{{ route('logout') }}"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        Logout
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</nav>
</div>
</header>
        <?php endif ?>
        <main class="container mt-4">