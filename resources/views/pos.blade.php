<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SuperPOS — New Sale</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    },
                    colors: {
                        indigo: {
                            50:  '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            500: '#6366f1',
                            600: '#4F46E5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                        emerald: {
                            400: '#34d399',
                            500: '#10B981',
                            600: '#059669',
                            700: '#047857',
                        },
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Scrollbar styling */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c7d2fe; border-radius: 10px; }

        /* Sidebar active state */
        .nav-item { transition: all 0.15s ease; }
        .nav-item:hover { background: rgba(255,255,255,0.12); }
        .nav-item.active { background: rgba(255,255,255,0.18); box-shadow: inset 3px 0 0 #fff; }

        /* Search bar focus ring */
        #searchInput:focus { box-shadow: 0 0 0 3px rgba(79,70,229,0.25); }

        /* Table row hover */
        .cart-row { transition: background 0.1s ease; }
        .cart-row:hover { background: #f0f4ff; }

        /* Pulse animation for search */
        @keyframes pulse-border {
            0%, 100% { box-shadow: 0 0 0 0 rgba(79,70,229,0.3); }
            50%       { box-shadow: 0 0 0 6px rgba(79,70,229,0); }
        }
        .search-active { animation: pulse-border 1.5s infinite; }

        /* Checkout button shine */
        #checkoutBtn::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: skewX(-20deg);
            transition: left 0.4s ease;
        }
        #checkoutBtn:hover::after { left: 140%; }
        #checkoutBtn { overflow: hidden; position: relative; }

        /* Fade-in for results */
        @keyframes fadeIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:translateY(0); } }
        .fade-in { animation: fadeIn 0.2s ease forwards; }

        /* Modal backdrop */
        #settingsModal { backdrop-filter: blur(4px); }

        /* Toast notification */
        #toast { transition: all 0.3s ease; }
    </style>
</head>

<body class="bg-[#F9FAFB] text-gray-800 antialiased">

<!-- ============================================================
     APP SHELL: Sidebar + Main Content
     ============================================================ -->
<div class="flex h-screen overflow-hidden">

    <!-- ===================== SIDEBAR ========================= -->
    <aside class="flex flex-col w-20 bg-indigo-600 shadow-xl flex-shrink-0">

        <!-- Logo -->
        <div class="flex items-center justify-center h-16 border-b border-indigo-500">
            <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center shadow">
                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 flex flex-col items-center py-4 gap-1">

            <a href="{{ route('dashboard') }}" id="navDashboard" class="nav-item w-14 h-14 rounded-xl flex flex-col items-center justify-center gap-1 text-indigo-200 hover:text-white cursor-pointer" title="Dashboard">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Dashboard</span>
            </a>

            <a href="{{ route('pos') }}" id="navSale" class="nav-item active w-14 h-14 rounded-xl flex flex-col items-center justify-center gap-1 text-white cursor-pointer" title="New Sale">
                <i data-lucide="scan-line" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">New Sale</span>
            </a>

            {{-- Admin/Manager Only Links --}}
            @if(Auth::user()->canManageInventory())
            <a href="#" id="navInventory" class="nav-item w-14 h-14 rounded-xl flex flex-col items-center justify-center gap-1 text-indigo-200 hover:text-white cursor-pointer" title="Inventory">
                <i data-lucide="package" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Inventory</span>
            </a>

            <a href="#" id="navCustomers" class="nav-item w-14 h-14 rounded-xl flex flex-col items-center justify-center gap-1 text-indigo-200 hover:text-white cursor-pointer" title="Customers">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Customers</span>
            </a>

            <a href="#" id="navReports" class="nav-item w-14 h-14 rounded-xl flex flex-col items-center justify-center gap-1 text-indigo-200 hover:text-white cursor-pointer" title="Reports">
                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Reports</span>
            </a>
            @endif
        </nav>

        <!-- Settings at bottom -->
        <div class="flex flex-col items-center pb-4 gap-1">
            <button onclick="openSettings()" class="nav-item w-14 h-14 rounded-xl flex flex-col items-center justify-center gap-1 text-indigo-200 hover:text-white cursor-pointer" title="Settings">
                <i data-lucide="settings" class="w-5 h-5"></i>
                <span class="text-[9px] font-medium">Settings</span>
            </button>
        </div>
    </aside>

    <!-- ===================== MAIN CONTENT ==================== -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Top Bar -->
        <header class="h-14 bg-white border-b border-gray-100 flex items-center justify-between px-6 flex-shrink-0 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="flex flex-col">
                    <h1 class="text-sm font-semibold text-gray-800 leading-none">New Sale</h1>
                    <p class="text-xs text-gray-400 mt-0.5">Scan or search products to begin</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <!-- Auth status badge -->
                <div id="authBadge" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span>
                    <span id="authBadgeText">Token not set</span>
                </div>
                <!-- Date / Time -->
                <div class="text-xs text-gray-400 tabular-nums" id="clockDisplay"></div>
                <!-- Cashier Avatar -->
                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow">
                    CS
                </div>
            </div>
        </header>

        <!-- Body: 70/30 Split -->
        <div class="flex-1 flex overflow-hidden p-4 gap-4">

            <!-- ============= LEFT PANEL: Products & Cart ======= -->
            <section class="flex flex-col w-[70%] gap-4 overflow-hidden">

                <!-- Search Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-3">
                        <i data-lucide="search" class="w-3 h-3 inline-block mr-1 -mt-0.5"></i>
                        Product Search / Barcode Scanner
                    </label>
                    <div class="relative flex gap-3">
                        <div class="relative flex-1">
                            <i data-lucide="scan-barcode" class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400"></i>
                            <input
                                id="searchInput"
                                type="text"
                                autocomplete="off"
                                placeholder="Scan barcode or type product name..."
                                class="w-full pl-12 pr-4 py-4 text-base bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-800 placeholder-gray-400 focus:outline-none focus:border-indigo-500 focus:bg-white transition-all duration-200"
                            >
                            <!-- Searching spinner -->
                            <div id="searchSpinner" class="hidden absolute right-4 top-1/2 -translate-y-1/2">
                                <svg class="animate-spin h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </div>
                        </div>
                        <button id="searchBtn" class="px-6 py-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold rounded-xl transition-colors duration-150 flex items-center gap-2 shadow-sm">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            Search
                        </button>
                    </div>

                    <!-- Search Status -->
                    <div id="searchStatus" class="mt-3 text-xs text-gray-400 flex items-center gap-1.5">
                        <i data-lucide="info" class="w-3 h-3"></i>
                        <span>Scan a barcode for instant match, or type a product name for fuzzy search.</span>
                    </div>

                    <!-- Search Results Dropdown -->
                    <div id="searchResults" class="mt-3 space-y-2"></div>
                </div>

                <!-- Cart Table Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col flex-1 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <i data-lucide="shopping-cart" class="w-4 h-4 text-indigo-500"></i>
                            <span class="text-sm font-semibold text-gray-700">Current Cart</span>
                            <span id="itemCount" class="ml-1 px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">0</span>
                        </div>
                        <button id="clearCartBtn" class="text-xs text-red-400 hover:text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1">
                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                            Clear Cart
                        </button>
                    </div>

                    <!-- Table Header -->
                    <div class="grid grid-cols-12 gap-2 px-5 py-2.5 bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        <div class="col-span-5">Item</div>
                        <div class="col-span-2 text-center">Qty</div>
                        <div class="col-span-2 text-right">Price</div>
                        <div class="col-span-2 text-right">Total</div>
                        <div class="col-span-1 text-center">Action</div>
                    </div>

                    <!-- Table Body -->
                    <div id="cartList" class="flex-1 overflow-y-auto">
                        <!-- Empty state -->
                        <div id="cartEmpty" class="flex flex-col items-center justify-center h-full py-12 text-gray-300">
                            <i data-lucide="shopping-cart" class="w-10 h-10 mb-3"></i>
                            <p class="text-sm font-medium">Your cart is empty</p>
                            <p class="text-xs mt-1">Search for products and add them here</p>
                        </div>
                    </div>

                    <!-- Cart Status -->
                    <div id="cartStatus" class="px-5 py-2.5 border-t border-gray-100 text-xs text-gray-400 flex items-center gap-1.5">
                        <i data-lucide="circle-dot" class="w-3 h-3 text-emerald-400"></i>
                        <span id="cartStatusText">Add items to the cart to start billing.</span>
                    </div>
                </div>
            </section>

            <!-- ============= RIGHT PANEL: Bill Summary ========= -->
            <aside class="w-[30%] flex flex-col gap-4 overflow-y-auto">

                <!-- Payment method -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">
                        Payment Method
                    </label>
                    <div class="grid grid-cols-2 gap-2" id="paymentMethods">
                        <button onclick="selectPayment('cash')" id="pm-cash" class="payment-btn active-pm flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 border-indigo-500 bg-indigo-50 text-indigo-700 text-sm font-semibold transition-all">
                            <i data-lucide="banknote" class="w-4 h-4"></i> Cash
                        </button>
                        <button onclick="selectPayment('card')" id="pm-card" class="payment-btn flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 border-gray-200 text-gray-500 text-sm font-medium hover:border-indigo-300 hover:bg-indigo-50 transition-all">
                            <i data-lucide="credit-card" class="w-4 h-4"></i> Card
                        </button>
                        <button onclick="selectPayment('check')" id="pm-check" class="payment-btn flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 border-gray-200 text-gray-500 text-sm font-medium hover:border-indigo-300 hover:bg-indigo-50 transition-all">
                            <i data-lucide="file-text" class="w-4 h-4"></i> Check
                        </button>
                        <button onclick="selectPayment('transfer')" id="pm-transfer" class="payment-btn flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 border-gray-200 text-gray-500 text-sm font-medium hover:border-indigo-300 hover:bg-indigo-50 transition-all">
                            <i data-lucide="arrow-left-right" class="w-4 h-4"></i> Transfer
                        </button>
                    </div>
                    <input type="hidden" id="paymentMethod" value="cash">
                </div>

                <!-- Bill Summary Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col gap-4">
                    <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                        <i data-lucide="receipt" class="w-4 h-4 text-indigo-500"></i>
                        Bill Summary
                    </h2>

                    <!-- Line items -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-semibold text-gray-800" id="subtotalAmt">Rs. 0.00</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Tax <span class="text-xs text-gray-400">(5%)</span></span>
                            <span class="font-semibold text-gray-800" id="taxAmt">Rs. 0.00</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2 flex-1">
                                <span class="text-gray-500">Discount</span>
                                <div class="relative flex items-center">
                                    <span class="absolute left-2.5 text-xs text-gray-400">Rs.</span>
                                    <input
                                        id="discountInput"
                                        type="number"
                                        min="0"
                                        value="0"
                                        class="w-20 pl-7 pr-2 py-1 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-400 text-right"
                                        oninput="recalculate()"
                                    >
                                </div>
                            </div>
                            <span class="font-semibold text-red-500" id="discountAmt">- Rs. 0.00</span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-dashed border-gray-200 my-1"></div>

                    <!-- Total -->
                    <div class="flex items-center justify-between">
                        <span class="text-base font-bold text-gray-800">Total</span>
                        <span id="totalAmount" class="text-2xl font-extrabold text-indigo-600">Rs. 0.00</span>
                    </div>

                    <!-- Cash tendered (visible when cash selected) -->
                    <div id="cashTendered" class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Cash Tendered</span>
                            <div class="relative flex items-center">
                                <span class="absolute left-2.5 text-xs text-gray-400">Rs.</span>
                                <input
                                    id="cashInput"
                                    type="number"
                                    min="0"
                                    placeholder="0.00"
                                    class="w-28 pl-7 pr-2 py-1 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-400 text-right"
                                    oninput="updateChange()"
                                >
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Change</span>
                            <span id="changeAmt" class="font-semibold text-emerald-600">Rs. 0.00</span>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <button id="checkoutBtn" onclick="checkout()" class="w-full bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-bold py-4 rounded-xl text-base flex items-center justify-center gap-2.5 shadow-md transition-colors duration-150 mt-1">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        Checkout
                    </button>

                    <!-- New Sale Button -->
                    <button onclick="newSale()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-2.5 rounded-xl text-sm flex items-center justify-center gap-2 transition-colors duration-150">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        New Sale
                    </button>
                </div>

                <!-- Quick Stats Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Quick Stats</h3>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="text-center">
                            <div class="text-lg font-bold text-indigo-600" id="statItems">0</div>
                            <div class="text-xs text-gray-400 mt-0.5">Items</div>
                        </div>
                        <div class="text-center border-x border-gray-100">
                            <div class="text-lg font-bold text-emerald-500" id="statSales">0</div>
                            <div class="text-xs text-gray-400 mt-0.5">Sales Today</div>
                        </div>
                        <div class="text-center">
                            <div class="text-lg font-bold text-amber-500" id="statPending">0</div>
                            <div class="text-xs text-gray-400 mt-0.5">Pending</div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<!-- ===================== SETTINGS MODAL ==================== -->
<div id="settingsModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 fade-in">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="settings-2" class="w-4 h-4 text-indigo-500"></i>
                POS Settings
            </h2>
            <button onclick="closeSettings()" class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">API Bearer Token</label>
                <div class="flex gap-2">
                    <input id="tokenInput" type="password" placeholder="Paste your Bearer token here" class="flex-1 px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-400 text-gray-700">
                    <button onclick="toggleTokenVisibility()" class="w-10 h-10 border border-gray-200 rounded-xl flex items-center justify-center text-gray-400 hover:text-indigo-600 hover:border-indigo-300 transition-colors">
                        <i data-lucide="eye" class="w-4 h-4" id="eyeIcon"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1.5">Required to authenticate API requests. Stored locally in your browser.</p>
            </div>

            <div class="flex gap-2 pt-1">
                <button onclick="saveToken()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl text-sm transition-colors flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Save Token
                </button>
                <button onclick="clearToken()" class="px-4 py-2.5 border border-red-200 text-red-500 hover:bg-red-50 font-semibold rounded-xl text-sm transition-colors">
                    Clear
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===================== TOAST NOTIFICATION ================ -->
<div id="toast" class="hidden fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl text-sm font-medium max-w-sm">
    <i id="toastIcon" class="w-4 h-4 flex-shrink-0"></i>
    <span id="toastText"></span>
</div>


<!-- ===================== JAVASCRIPT ======================== -->
<script>
    // ── CONFIG ────────────────────────────────────────────────
    const API_BASE   = '/api';
    const TAX_RATE   = 0.05; // 5%

    // ── ELEMENT REFS ──────────────────────────────────────────
    const $  = id => document.getElementById(id);
    const searchInput   = $('searchInput');
    const searchResults = $('searchResults');
    const searchStatus  = $('searchStatus').querySelector('span');
    const searchSpinner = $('searchSpinner');
    const cartList      = $('cartList');
    const cartEmpty     = $('cartEmpty');
    const cartStatusText= $('cartStatusText');
    const itemCount     = $('itemCount');
    const statItems     = $('statItems');
    const totalAmount   = $('totalAmount');
    const subtotalAmt   = $('subtotalAmt');
    const taxAmt        = $('taxAmt');
    const discountAmt   = $('discountAmt');
    const changeAmt     = $('changeAmt');
    const discountInput = $('discountInput');
    const cashInput     = $('cashInput');
    const tokenInput    = $('tokenInput');

    let searchTimer = null;
    let currentPayment = 'cash';
    let currentCartItems = [];
    let subtotalRaw = 0;

    // ── CLOCK ────────────────────────────────────────────────
    function updateClock() {
        const now = new Date();
        $('clockDisplay').textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── TOKEN MANAGEMENT ─────────────────────────────────────
    function getToken()      { return localStorage.getItem('pos_token') || ''; }
    function saveToken()     { setToken(tokenInput.value); showToast('Token saved successfully.', 'success'); closeSettings(); }
    function clearToken()    { localStorage.removeItem('pos_token'); tokenInput.value = ''; updateAuthBadge(); showToast('Token cleared.', 'info'); }
    function setToken(t)     { localStorage.setItem('pos_token', t.trim()); tokenInput.value = t.trim(); updateAuthBadge(); }

    function updateAuthBadge() {
        const hasToken = !!getToken();
        const badge = $('authBadge');
        const dot   = badge.querySelector('span');
        if (hasToken) {
            badge.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 border border-emerald-200';
            dot.className   = 'w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block';
            $('authBadgeText').textContent = 'Token active';
        } else {
            badge.className = 'flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-200';
            dot.className   = 'w-1.5 h-1.5 rounded-full bg-amber-400 inline-block';
            $('authBadgeText').textContent = 'Token not set';
        }
    }

    function toggleTokenVisibility() {
        const inp = tokenInput;
        const icon = $('eyeIcon');
        if (inp.type === 'password') { inp.type = 'text'; icon.setAttribute('data-lucide', 'eye-off'); }
        else                         { inp.type = 'password'; icon.setAttribute('data-lucide', 'eye'); }
        lucide.createIcons();
    }

    function openSettings()  { $('settingsModal').classList.remove('hidden'); tokenInput.value = getToken(); }
    function closeSettings() { $('settingsModal').classList.add('hidden'); }

    // Close modal on backdrop click
    $('settingsModal').addEventListener('click', e => { if (e.target === $('settingsModal')) closeSettings(); });

    // ── API HELPER ───────────────────────────────────────────
    function apiHeaders() {
        const t = getToken();
        return {
            'Content-Type': 'application/json',
            'Authorization': t ? `Bearer ${t}` : '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        };
    }

    async function api(path, options = {}) {
        const response = await fetch(`${API_BASE}${path}`, {
            ...options,
            headers: { ...apiHeaders(), ...(options.headers || {}) },
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data?.message || 'Request failed');
        return data;
    }

    // ── MONEY FORMAT ─────────────────────────────────────────
    function money(v)   { return `Rs. ${Number(v||0).toFixed(2)}`; }
    function raw(v)     { return Number(v||0); }

    // ── PAYMENT SELECTION ────────────────────────────────────
    function selectPayment(method) {
        currentPayment = method;
        $('paymentMethod').value = method;
        document.querySelectorAll('.payment-btn').forEach(btn => {
            btn.className = btn.className
                .replace('border-indigo-500 bg-indigo-50 text-indigo-700', '')
                .replace('active-pm', '')
                .trim();
            btn.classList.add('border-gray-200', 'text-gray-500');
        });
        const active = $(`pm-${method}`);
        active.classList.remove('border-gray-200', 'text-gray-500');
        active.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');

        // Show / hide cash tendered section
        $('cashTendered').style.display = method === 'cash' ? 'block' : 'none';
    }

    // ── SEARCH ───────────────────────────────────────────────
    async function searchProducts() {
        const term = searchInput.value.trim();
        if (!term) { searchResults.innerHTML = ''; setSearchStatus('Scan a barcode for instant match, or type a product name.', false); return; }

        setSearchStatus('Searching...', false);
        searchSpinner.classList.remove('hidden');

        try {
            const result = await api(`/products/search?query=${encodeURIComponent(term)}`);
            const items  = result.data || [];
            renderSearchResults(items);
            setSearchStatus(`${items.length} product(s) found.`, false);
        } catch (err) {
            searchResults.innerHTML = '';
            setSearchStatus(err.message === 'Product not found' ? 'Product not found' : 'Search failed. Please try again.', true);
        } finally {
            searchSpinner.classList.add('hidden');
        }
    }

    function setSearchStatus(msg, isError) {
        const el = $('searchStatus');
        el.querySelector('span').textContent = msg;
        el.querySelector('[data-lucide]').setAttribute('data-lucide', isError ? 'alert-circle' : 'info');
        el.className = `mt-3 text-xs flex items-center gap-1.5 ${isError ? 'text-red-400' : 'text-gray-400'}`;
        lucide.createIcons();
    }

    function renderSearchResults(items) {
        if (!items.length) { searchResults.innerHTML = ''; return; }

        searchResults.innerHTML = `
            <div class="grid gap-2">
                ${items.map(item => `
                    <div class="fade-in flex items-center justify-between p-3 bg-gray-50 hover:bg-indigo-50 border border-gray-100 hover:border-indigo-200 rounded-xl transition-all group">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="package" class="w-4 h-4 text-indigo-500"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-gray-800 truncate">${escHtml(item.name)}</div>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-400">Stock: <strong class="${item.stock > 10 ? 'text-emerald-600' : 'text-amber-500'}">${item.stock}</strong></span>
                                    <span class="text-xs font-bold text-indigo-600">${money(item.price)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0 ml-3">
                            <input class="w-16 text-center px-2 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-400" type="number" min="1" max="${item.stock}" value="1" data-qty-for="${item.id}">
                            <button data-add-product="${item.id}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-1">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add
                            </button>
                        </div>
                    </div>
                `).join('')}
            </div>`;

        lucide.createIcons();

        searchResults.querySelectorAll('[data-add-product]').forEach(btn => {
            btn.addEventListener('click', () => {
                const id  = btn.getAttribute('data-add-product');
                const qty = Number(searchResults.querySelector(`[data-qty-for="${id}"]`).value || 1);
                addToCart(id, qty);
            });
        });
    }

    // ── CART ─────────────────────────────────────────────────
    async function refreshCart() {
        try {
            const result = await api('/billing/cart/summary');
            renderCart(result.data);
        } catch (err) {
            updateCartStatus(err.message, true);
        }
    }

    async function addToCart(productId, quantity) {
        try {
            const result = await api('/billing/cart/items', {
                method: 'POST',
                body: JSON.stringify({ product_id: Number(productId), quantity }),
            });
            renderCart(result.data);
            updateCartStatus('Item added to cart.', false);
            showToast('Item added to cart.', 'success');
        } catch (err) {
            updateCartStatus(err.message, true);
            showToast(err.message, 'error');
        }
    }

    async function removeFromCart(productId) {
        try {
            const result = await api(`/billing/cart/items/${productId}`, { method: 'DELETE' });
            renderCart(result.data);
            updateCartStatus('Item removed.', false);
        } catch (err) {
            updateCartStatus(err.message, true);
        }
    }

    function renderCart(summary) {
        currentCartItems = summary.items || [];
        subtotalRaw      = raw(summary.total_amount);

        const count = currentCartItems.length;
        itemCount.textContent = count;
        statItems.textContent = count;

        if (!count) {
            cartList.innerHTML = '';
            cartList.appendChild(cartEmpty);
            cartEmpty.classList.remove('hidden');
            resetBill();
            return;
        }

        // Remove empty state
        cartEmpty.classList.add('hidden');

        cartList.innerHTML = currentCartItems.map((item, idx) => `
            <div class="cart-row grid grid-cols-12 gap-2 items-center px-5 py-3 border-b border-gray-50 fade-in">
                <div class="col-span-5 flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-bold text-indigo-500">${idx+1}</span>
                    </div>
                    <span class="text-sm font-medium text-gray-800 truncate">${escHtml(item.name)}</span>
                </div>
                <div class="col-span-2 text-center">
                    <span class="inline-flex items-center justify-center w-7 h-6 bg-gray-100 rounded-md text-xs font-bold text-gray-600">${item.quantity}</span>
                </div>
                <div class="col-span-2 text-right text-sm text-gray-600 tabular-nums">Rs.${raw(item.price).toFixed(2)}</div>
                <div class="col-span-2 text-right text-sm font-semibold text-gray-800 tabular-nums">Rs.${raw(item.line_total).toFixed(2)}</div>
                <div class="col-span-1 flex justify-center">
                    <button onclick="removeFromCart(${item.product_id || item.id})" class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 hover:text-red-600 flex items-center justify-center transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        `).join('');

        recalculate();
    }

    function resetBill() {
        subtotalAmt.textContent = 'Rs. 0.00';
        taxAmt.textContent      = 'Rs. 0.00';
        discountAmt.textContent = '- Rs. 0.00';
        totalAmount.textContent = 'Rs. 0.00';
        changeAmt.textContent   = 'Rs. 0.00';
    }

    function recalculate() {
        const discount = Math.max(0, raw(discountInput.value));
        const tax      = subtotalRaw * TAX_RATE;
        const total    = Math.max(0, subtotalRaw + tax - discount);

        subtotalAmt.textContent = money(subtotalRaw);
        taxAmt.textContent      = money(tax);
        discountAmt.textContent = `- ${money(discount)}`;
        totalAmount.textContent = money(total);
        updateChange();
    }

    function updateChange() {
        const total  = raw(totalAmount.textContent.replace('Rs.','').trim());
        const tendered = raw(cashInput.value);
        const change   = tendered - total;
        changeAmt.textContent = change >= 0 ? money(change) : 'Rs. 0.00';
        changeAmt.className   = `font-semibold ${change >= 0 ? 'text-emerald-600' : 'text-red-400'}`;
    }

    function updateCartStatus(msg, isError) {
        cartStatusText.textContent = msg;
        const icon = $('cartStatus').querySelector('[data-lucide]');
        icon.setAttribute('data-lucide', isError ? 'alert-circle' : 'circle-dot');
        icon.className = `w-3 h-3 ${isError ? 'text-red-400' : 'text-emerald-400'}`;
        lucide.createIcons();
    }

    // ── CHECKOUT ─────────────────────────────────────────────
    async function checkout() {
        if (!currentCartItems.length) { showToast('Cart is empty.', 'error'); return; }
        const btn = $('checkoutBtn');
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Processing...`;
        try {
            const result = await api('/billing/checkout', {
                method: 'POST',
                body: JSON.stringify({ payment_method: currentPayment }),
            });
            const saleId = result.data?.sale_id;
            const total  = result.data?.total_amount;
            showToast(`✓ Sale #${saleId} completed — ${money(total)}`, 'success');
            updateCartStatus(`Checkout complete. Sale #${saleId} total ${money(total)}.`, false);
            searchResults.innerHTML = '';
            statItems.textContent   = 0;
            await refreshCart();
        } catch (err) {
            updateCartStatus(err.message, true);
            showToast(err.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = `<i data-lucide="check-circle" class="w-5 h-5"></i> Checkout`;
            lucide.createIcons();
        }
    }

    function newSale() {
        searchInput.value       = '';
        searchResults.innerHTML = '';
        discountInput.value     = 0;
        cashInput.value         = '';
        setSearchStatus('Scan a barcode for instant match, or type a product name.', false);
        searchInput.focus();
    }

    // ── CLEAR CART ───────────────────────────────────────────
    $('clearCartBtn').addEventListener('click', async () => {
        if (!currentCartItems.length) return;
        // Quick optimistic clear (no dedicated API assumed—refresh will sync)
        cartList.innerHTML = '';
        cartList.appendChild(cartEmpty);
        cartEmpty.classList.remove('hidden');
        currentCartItems = [];
        subtotalRaw = 0;
        resetBill();
        itemCount.textContent = 0;
        statItems.textContent = 0;
        updateCartStatus('Cart cleared.', false);
    });

    // ── SEARCH EVENTS ────────────────────────────────────────
    $('searchBtn').addEventListener('click', searchProducts);
    searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') searchProducts(); });
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(searchProducts, 280);
    });

    // ── TOAST ────────────────────────────────────────────────
    let toastTimer = null;
    function showToast(msg, type = 'info') {
        const toast = $('toast');
        const icon  = $('toastIcon');
        const text  = $('toastText');

        const styles = {
            success: { bg: 'bg-emerald-600', icon: 'check-circle' },
            error:   { bg: 'bg-red-500',     icon: 'alert-circle'  },
            info:    { bg: 'bg-indigo-600',   icon: 'info'          },
        };
        const s = styles[type] || styles.info;
        toast.className = `fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl text-sm font-medium text-white max-w-sm ${s.bg}`;
        icon.setAttribute('data-lucide', s.icon);
        text.textContent = msg;
        toast.classList.remove('hidden');
        lucide.createIcons();

        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.add('hidden'), 3500);
    }

    // ── ESCAPE KEY closes modal ───────────────────────────────
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSettings(); });

    // ── INIT ─────────────────────────────────────────────────
    tokenInput.value = getToken();
    updateAuthBadge();
    selectPayment('cash');
    refreshCart();
    searchInput.focus();
    lucide.createIcons();

    // ── UTILITY ──────────────────────────────────────────────
    function escHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }
</script>
</body>
</html>
