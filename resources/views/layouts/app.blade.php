<!DOCTYPE html>
<html>

<head>

    <title>Dashboard</title>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {

            background: #f1f5f9;

            font-family:
                'Segoe UI',
                sans-serif;

        }


        /* SIDEBAR */
        .sidebar {

            width: 260px;

            height: 100vh;

            position: fixed;

            left: 0;

            top: 0;

            overflow-y: auto;

            overflow-x: hidden;

            background:
                linear-gradient(180deg,
                    #1e293b,
                    #0f172a);

            padding: 15px;

            display: flex;

            flex-direction: column;

            color: white;

            box-shadow:
                4px 0 10px rgba(0, 0, 0, .08);

        }


        .sidebar h4 {

            margin-bottom: 30px;

            font-weight: 600;

        }


        .sidebar a,
        .menu-toggle {

            display: flex;

            align-items: center;

            gap: 10px;

            padding: 12px;

            margin: 8px 0;

            border-radius: 10px;

            color: #cbd5e1;

            text-decoration: none;

            transition: .3s;

            font-size: 14px;

            cursor: pointer;

        }


        .sidebar a:hover,
        .menu-toggle:hover {

            background: #334155;

            color: white;

            transform:
                translateX(5px);

        }


        .sidebar a.active,
        .menu-toggle.active {

            background: #2563eb;

            color: white;

            font-weight: 600;

        }



        /* DROPDOWN */

        .menu-group {

            margin-top: 5px;

        }


        .menu-toggle {

            justify-content: space-between;

        }


        .submenu {

            display: none;

            padding-left: 16px;

        }


        .submenu.show {

            display: block;

        }


        .submenu a {

            background: #172033;

            font-size: 13px;

            padding: 10px;

            margin: 6px 0;

        }


        .arrow {

            transition: .3s;

        }


        .arrow.rotate {

            transform:
                rotate(90deg);

        }



        /* CONTENT */

        .content {

            margin-left: 260px;

            padding: 25px;

            min-height: 100vh;

        }



        /* CARD */

        .card-modern {

            background: white;

            border-radius: 16px;

            padding: 20px;

            box-shadow:
                0 4px 12px rgba(0, 0, 0, .05);

        }



        /* TABLE */

        .table {

            background: white;

        }


        .table th {

            background: #f8fafc;

            font-size: 13px;

        }


        .nowrap-table th,
        .nowrap-table td {

            white-space: nowrap;

            font-size: 12px;

        }



        /* LOGOUT */

        .logout-wrapper {

            margin-top: auto;

            padding-top: 20px;

        }


        .btn-logout {

            width: 100%;

            border: none;

            padding: 12px;

            border-radius: 12px;

            font-weight: 600;

            color: white;

            background:
                linear-gradient(135deg,
                    #dc2626,
                    #ef4444);

            transition: .3s;

            box-shadow:
                0 8px 20px rgba(239, 68, 68, .25);

        }


        .btn-logout:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 12px 25px rgba(239, 68, 68, .35);

        }
    </style>

</head>

<body>


    <!-- SIDEBAR -->
    <div class="sidebar">

        <div>

            <h4>

                Master Hub System

            </h4>
            <span class="badge bg-secondary px-3 py-2">
                Version 06.24
            </span>

            <!-- DASHBOARD -->
            <a href="/" class="{{ request()->is('/') ? 'active' : '' }}">

                📊 Dashboard

            </a>


            <!-- BULK -->
            <a href="/bulk" class="{{ request()->is('bulk') ? 'active' : '' }}">

                📧 BC Email

            </a>


            <!-- UPLOAD -->
            <a href="/upload" class="{{ request()->is('upload') ? 'active' : '' }}">

                ⬆️ Upload Sheet

            </a>



            @auth

                @php
                    $role = auth()->user()->role;

                    $isAdmin = $role == 'admin';
                    $isUser = $role == 'user';

                    $masterOpen =
                        request()->is('template-master*') ||
                        request()->is('email-master*') ||
                        request()->is('footer-master*') ||
                        request()->is('user-master*');
                @endphp

                @if($isAdmin || $isUser)



                    <div class="menu-group">


                        <div class="menu-toggle {{ $masterOpen ? 'active' : '' }}" onclick="toggleMaster()">

                            <span>

                                📁 Master

                            </span>

                            <span id="masterArrow" class="arrow {{ $masterOpen ? 'rotate' : '' }}">

                                ▶

                            </span>

                        </div>



                        <div id="masterMenu" class="submenu {{ $masterOpen ? 'show' : '' }}">

                            <a href="/template-master" class="{{ request()->is('template-master*') ? 'active' : '' }}">
                                📄 Template
                            </a>

                            <a href="/email-master" class="{{ request()->is('email-master*') ? 'active' : '' }}">
                                📬 Email
                            </a>

                            <a href="/footer-master" class="{{ request()->is('footer-master*') ? 'active' : '' }}">
                                📝 Footer
                            </a>

                            {{-- User Master --}}
                            @if(
                                in_array(
                                    auth()->user()->role,
                                    ['master','admin','user']
                                )
                            )

                            <a href="/user-master"
                            class="{{ request()->is('user-master*') ? 'active' : '' }}">
                                👤 Akun
                            </a>

                            @endif

                        </div>
                    </div>

                @endif

            @endauth


        </div>



        @auth

            <div class="logout-wrapper">

                <form action="/logout" method="POST">

                    @csrf

                    <button class="btn-logout" onclick="return confirm('Yakin ingin logout?')">

                        🚪 Logout

                    </button>

                </form>

            </div>

        @endauth


    </div>



    <!-- CONTENT -->
    <div class="content">


        @if(session('success'))

            <div class="alert alert-success alert-dismissible fade show shadow-sm" style="
                border-radius:12px;
                font-weight:500;
                ">

                ✅ {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert">
                </button>

            </div>

        @endif



        @if(session('error'))

            <div class="alert alert-danger alert-dismissible fade show shadow-sm" style="
                border-radius:12px;
                font-weight:500;
                ">

                ❌ {{ session('error') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert">
                </button>

            </div>

        @endif



        <div class="card-modern">

            @yield('content')

        </div>

    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>


    <script>

        function toggleMaster() {

            let menu =
                document.getElementById(
                    'masterMenu'
                );

            let arrow =
                document.getElementById(
                    'masterArrow'
                );

            menu.classList.toggle(
                'show'
            );

            arrow.classList.toggle(
                'rotate'
            );

        }

    </script>

</body>

</html>