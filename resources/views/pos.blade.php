<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Billing</title>
    <style>
        :root {
            --bg: #0f172a;
            --panel: #111827;
            --panel-2: #1f2937;
            --text: #e5e7eb;
            --muted: #9ca3af;
            --accent: #22c55e;
            --accent-2: #38bdf8;
            --danger: #ef4444;
            --border: rgba(255,255,255,0.08);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, Segoe UI, Arial, sans-serif;
            background: radial-gradient(circle at top left, #1e293b, var(--bg) 55%);
            color: var(--text);
        }

        .shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 16px;
            padding: 16px;
        }

        .card {
            background: rgba(17, 24, 39, 0.92);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.24);
            overflow: hidden;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 20px;
            border-bottom: 1px solid var(--border);
        }

        .title {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
        }

        .subtitle {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 13px;
        }

        .content { padding: 16px 20px 20px; }

        .grid { display: grid; gap: 12px; }
        .grid-2 { grid-template-columns: 1.2fr 0.8fr; }

        .field-row { display: grid; grid-template-columns: 1fr auto; gap: 10px; }
        .field-row-2 { display: grid; grid-template-columns: 1fr 160px; gap: 10px; }

        input, select, button {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 14px;
            font-size: 15px;
            outline: none;
        }

        input, select {
            background: var(--panel-2);
            color: var(--text);
            width: 100%;
        }

        input::placeholder { color: #94a3b8; }

        button {
            background: linear-gradient(135deg, var(--accent), #16a34a);
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.12s ease, opacity 0.12s ease;
        }

        button:hover { transform: translateY(-1px); }
        button.secondary { background: var(--panel-2); }
        button.danger { background: linear-gradient(135deg, var(--danger), #b91c1c); }
        button.ghost { background: transparent; }

        .search-results, .cart-list {
            display: grid;
            gap: 10px;
        }

        .item, .cart-item {
            background: rgba(31, 41, 55, 0.7);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 14px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
        }

        .item h4, .cart-item h4 { margin: 0 0 6px; font-size: 15px; }
        .meta { color: var(--muted); font-size: 12px; }
        .price { font-weight: 700; color: #d1fae5; }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(56, 189, 248, 0.12);
            color: #7dd3fc;
            border: 1px solid rgba(56, 189, 248, 0.2);
            font-size: 12px;
        }

        .summary {
            display: grid;
            gap: 12px;
            padding: 16px 20px 20px;
        }

        .summary-box {
            background: rgba(31, 41, 55, 0.7);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            color: var(--text);
        }

        .total {
            font-size: 22px;
            font-weight: 800;
            color: #a7f3d0;
            border-top: 1px dashed var(--border);
            margin-top: 6px;
            padding-top: 14px;
        }

        .muted { color: var(--muted); }
        .small { font-size: 12px; }

        .status {
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 13px;
            background: rgba(56, 189, 248, 0.12);
            color: #7dd3fc;
            border: 1px solid rgba(56, 189, 248, 0.18);
        }

        .status.error {
            background: rgba(239, 68, 68, 0.12);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.18);
        }

        .topbar {
            display: grid;
            grid-template-columns: 1fr 240px;
            gap: 12px;
            margin-bottom: 12px;
        }

        .cart-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .qty-input { width: 92px; padding: 10px 12px; }

        @media (max-width: 1100px) {
            .shell { grid-template-columns: 1fr; }
            .grid-2, .topbar, .field-row, .field-row-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <section class="card">
            <div class="header">
                <div>
                    <h1 class="title">POS Billing</h1>
                    <p class="subtitle">Fast cashier workflow for search, cart, and checkout.</p>
                </div>
                <span class="pill" id="authState">Token not set</span>
            </div>

            <div class="content grid">
                <div class="topbar">
                    <input id="tokenInput" type="password" placeholder="Paste Bearer token here">
                    <button class="secondary" id="saveTokenBtn">Save Token</button>
                </div>

                <div class="field-row">
                    <input id="searchInput" type="text" placeholder="Scan barcode or type product name">
                    <button id="searchBtn">Search</button>
                </div>

                <div id="searchStatus" class="status">Search by barcode for fastest exact match, or type a name.</div>

                <div id="searchResults" class="search-results"></div>
            </div>
        </section>

        <aside class="card">
            <div class="header">
                <div>
                    <h2 class="title" style="font-size:18px;">Cart</h2>
                    <p class="subtitle">Temporary cart held on the backend.</p>
                </div>
                <button class="danger" id="clearTokenBtn">Clear Token</button>
            </div>

            <div class="summary">
                <div class="summary-box">
                    <div class="field-row-2">
                        <select id="paymentMethod">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="check">Check</option>
                            <option value="transfer">Transfer</option>
                        </select>
                        <button id="checkoutBtn">Checkout</button>
                    </div>
                </div>

                <div class="summary-box">
                    <div class="summary-line"><span class="muted">Items</span><strong id="itemCount">0</strong></div>
                    <div class="summary-line total"><span>Total</span><span id="totalAmount">0.00</span></div>
                </div>

                <div id="cartList" class="cart-list"></div>
                <div id="cartStatus" class="status">Add items to the cart to start billing.</div>
            </div>
        </aside>
    </div>

    <script>
        const API_BASE = '/api';
        const tokenInput = document.getElementById('tokenInput');
        const authState = document.getElementById('authState');
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const searchStatus = document.getElementById('searchStatus');
        const cartList = document.getElementById('cartList');
        const cartStatus = document.getElementById('cartStatus');
        const itemCount = document.getElementById('itemCount');
        const totalAmount = document.getElementById('totalAmount');
        const paymentMethod = document.getElementById('paymentMethod');

        let searchTimer = null;

        function getToken() {
            return localStorage.getItem('pos_token') || '';
        }

        function setToken(token) {
            localStorage.setItem('pos_token', token.trim());
            tokenInput.value = token.trim();
            updateAuthState();
        }

        function clearToken() {
            localStorage.removeItem('pos_token');
            tokenInput.value = '';
            updateAuthState();
        }

        function updateAuthState() {
            const token = getToken();
            authState.textContent = token ? 'Token saved' : 'Token not set';
            authState.style.background = token ? 'rgba(34,197,94,0.12)' : 'rgba(56,189,248,0.12)';
            authState.style.color = token ? '#86efac' : '#7dd3fc';
        }

        function money(value) {
            return Number(value || 0).toFixed(2);
        }

        function headers() {
            const token = getToken();
            return {
                'Content-Type': 'application/json',
                'Authorization': token ? `Bearer ${token}` : '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            };
        }

        async function api(path, options = {}) {
            const response = await fetch(`${API_BASE}${path}`, {
                ...options,
                headers: {
                    ...headers(),
                    ...(options.headers || {}),
                },
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                const message = data?.message || 'Request failed';
                throw new Error(message);
            }
            return data;
        }

        function renderSearchResults(items) {
            if (!items.length) {
                searchResults.innerHTML = '';
                return;
            }

            searchResults.innerHTML = items.map((item) => `
                <div class="item">
                    <div>
                        <h4>${item.name}</h4>
                        <div class="meta">Stock: ${item.stock}</div>
                        <div class="price">${money(item.price)}</div>
                    </div>
                    <div class="cart-actions">
                        <input class="qty-input" type="number" min="1" value="1" data-qty-for="${item.id}">
                        <button data-add-product="${item.id}">Add</button>
                    </div>
                </div>
            `).join('');

            searchResults.querySelectorAll('[data-add-product]').forEach((button) => {
                button.addEventListener('click', () => {
                    const productId = button.getAttribute('data-add-product');
                    const qtyInput = searchResults.querySelector(`[data-qty-for="${productId}"]`);
                    addToCart(productId, Number(qtyInput.value || 1));
                });
            });
        }

        function renderCart(summary) {
            itemCount.textContent = summary.items.length;
            totalAmount.textContent = money(summary.total_amount);

            if (!summary.items.length) {
                cartList.innerHTML = '<div class="muted small">Cart is empty.</div>';
                return;
            }

            cartList.innerHTML = summary.items.map((item) => `
                <div class="cart-item">
                    <div>
                        <h4>${item.name}</h4>
                        <div class="meta">Qty: ${item.quantity} x ${money(item.price)}</div>
                    </div>
                    <div class="price">${money(item.line_total)}</div>
                </div>
            `).join('');
        }

        async function refreshCart() {
            try {
                const result = await api('/billing/cart/summary');
                renderCart(result.data);
                cartStatus.textContent = 'Cart updated.';
                cartStatus.classList.remove('error');
            } catch (error) {
                cartStatus.textContent = error.message;
                cartStatus.classList.add('error');
            }
        }

        async function addToCart(productId, quantity) {
            try {
                const result = await api('/billing/cart/items', {
                    method: 'POST',
                    body: JSON.stringify({ product_id: Number(productId), quantity }),
                });

                renderCart(result.data);
                cartStatus.textContent = 'Item added to cart.';
                cartStatus.classList.remove('error');
            } catch (error) {
                cartStatus.textContent = error.message;
                cartStatus.classList.add('error');
            }
        }

        async function searchProducts() {
            const term = searchInput.value.trim();
            if (!term) {
                searchResults.innerHTML = '';
                return;
            }

            const query = /^\d+$/.test(term)
                ? `?barcode=${encodeURIComponent(term)}`
                : `?name=${encodeURIComponent(term)}`;

            searchStatus.textContent = 'Searching...';
            searchStatus.classList.remove('error');

            try {
                const result = await api(`/products/search${query}`);
                renderSearchResults(result.data || []);
                searchStatus.textContent = `${(result.data || []).length} product(s) found.`;
            } catch (error) {
                searchStatus.textContent = error.message;
                searchStatus.classList.add('error');
            }
        }

        async function checkout() {
            try {
                const result = await api('/billing/checkout', {
                    method: 'POST',
                    body: JSON.stringify({ payment_method: paymentMethod.value }),
                });

                cartStatus.textContent = `Checkout complete. Sale #${result.data.sale_id} total ${money(result.data.total_amount)}.`;
                cartStatus.classList.remove('error');
                searchResults.innerHTML = '';
                await refreshCart();
            } catch (error) {
                cartStatus.textContent = error.message;
                cartStatus.classList.add('error');
            }
        }

        document.getElementById('searchBtn').addEventListener('click', searchProducts);
        document.getElementById('checkoutBtn').addEventListener('click', checkout);
        document.getElementById('saveTokenBtn').addEventListener('click', () => setToken(tokenInput.value));
        document.getElementById('clearTokenBtn').addEventListener('click', () => {
            clearToken();
            cartStatus.textContent = 'Token cleared.';
            cartStatus.classList.remove('error');
        });

        tokenInput.value = getToken();
        updateAuthState();
        refreshCart();

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(searchProducts, 220);
        });
    </script>
</body>
</html>
