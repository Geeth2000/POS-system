<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            @if(in_array(Auth::user()->role, ['admin', 'manager']))
                                <li class="nav-item dropdown me-3" id="notificationDropdownContainer">
                                    <a id="notificationDropdown" class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        🔔
                                        <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                                            0
                                        </span>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg" aria-labelledby="notificationDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                                        <div class="p-2 border-bottom fw-bold bg-light">Notifications</div>
                                        <div id="notificationList">
                                            <div class="p-3 text-center text-muted">No new notifications</div>
                                        </div>
                                    </div>
                                </li>
                            @endif

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'manager']))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fetchNotifications = () => {
                axios.get('/api/notifications', {
                    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('auth_token') } // Assuming token is used or session based
                })
                .then(response => {
                    const notifications = response.data.data;
                    const badge = document.getElementById('notificationBadge');
                    const list = document.getElementById('notificationList');

                    if (notifications.length > 0) {
                        badge.style.display = 'inline-block';
                        badge.innerText = notifications.length;

                        list.innerHTML = '';
                        notifications.forEach(notif => {
                            const data = notif.data;
                            const date = new Date(notif.created_at).toLocaleString();
                            
                            list.innerHTML += `
                                <div class="dropdown-item border-bottom p-3 d-flex flex-column text-wrap" id="notif-${notif.id}">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong>⚠️ Low Stock Alert</strong>
                                        <small class="text-muted">${date}</small>
                                    </div>
                                    <div>Product: ${data.name} <small>(${data.sku})</small></div>
                                    <div class="text-danger">Current Stock: ${data.stock_qty} (Threshold: ${data.threshold})</div>
                                    <div class="mt-2 text-end">
                                        <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="resolveNotification('${notif.id}', event)">Mark as Resolved</button>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        badge.style.display = 'none';
                        list.innerHTML = '<div class="p-3 text-center text-muted">No new notifications</div>';
                    }
                })
                .catch(err => console.error('Error fetching notifications:', err));
            };

            window.resolveNotification = (id, event) => {
                event.stopPropagation(); // keep dropdown open
                axios.patch(`/api/notifications/${id}/resolve`)
                    .then(res => {
                        if (res.data.success) {
                            fetchNotifications();
                        }
                    })
                    .catch(err => console.error(err));
            };

            // Fetch on load
            fetchNotifications();

            // Poll every 15 seconds
            setInterval(fetchNotifications, 15000);
        });
    </script>
    @endif
</body>
</html>
