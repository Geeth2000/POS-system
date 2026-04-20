@extends('layouts.dashboard')

@section('title', 'Inventory Management')
@section('page-title', 'Inventory Management')
@section('page-subtitle', 'Manage your products, stock levels, and pricing')

@section('content')

{{-- ── Header & Add Button ────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-sm text-gray-500">
            Total Products: <span class="font-bold text-gray-800">{{ $products->total() }}</span>
        </p>
    </div>
    <button onclick="openAddModal()"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all shadow-lg shadow-indigo-100 hover:scale-[1.02] active:scale-95"
            style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add New Product
    </button>
</div>

{{-- ── Inventory Table ──────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50 text-gray-500 text-xs font-semibold uppercase tracking-wider">
                <th class="text-left px-6 py-4">SKU / Item Code</th>
                <th class="text-left px-6 py-4">Product Name</th>
                <th class="text-left px-6 py-4">Category</th>
                <th class="text-right px-6 py-4">Price</th>
                <th class="text-center px-6 py-4">Stock</th>
                <th class="text-center px-6 py-4">Status</th>
                <th class="text-right px-6 py-4">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($products as $product)
            <tr class="hover:bg-gray-50 transition-colors group">
                {{-- SKU --}}
                <td class="px-6 py-4 font-mono text-xs text-indigo-600 font-semibold">
                    {{ $product->sku ?? $product->item_code }}
                </td>

                {{-- Name --}}
                <td class="px-6 py-4">
                    <p class="font-medium text-gray-800">{{ $product->name }}</p>
                    @if($product->description)
                        <p class="text-xs text-gray-400 truncate max-w-[200px]">{{ $product->description }}</p>
                    @endif
                </td>

                {{-- Category --}}
                <td class="px-6 py-4">
                    <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 text-[10px] font-bold uppercase">
                        {{ $product->category->name ?? 'Uncategorized' }}
                    </span>
                </td>

                {{-- Price --}}
                <td class="px-6 py-4 text-right font-bold text-gray-900 tabular-nums">
                    Rs. {{ number_format($product->price, 2) }}
                </td>

                {{-- Stock --}}
                <td class="px-6 py-4 text-center">
                    <div class="inline-flex flex-col items-center">
                        <span class="text-sm font-bold {{ $product->stock_qty <= 10 ? 'text-red-500' : 'text-gray-700' }}">
                            {{ $product->stock_qty }}
                        </span>
                        @if($product->stock_qty <= 10)
                            <span class="text-[9px] font-bold text-red-400 uppercase tracking-tighter">Low Stock</span>
                        @endif
                    </div>
                </td>

                {{-- Status --}}
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                        {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        <span class="w-1 h-1 rounded-full {{ $product->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>

                {{-- Actions --}}
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        {{-- Edit Button --}}
                        <button onclick='openEditModal(@json($product))'
                                class="p-2 rounded-lg text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 transition-colors"
                                title="Edit Product">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </button>

                        {{-- Delete Button --}}
                        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="p-2 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 transition-colors"
                                    title="Delete Product">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-20 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-4 opacity-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="font-medium">No products found in inventory.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
        {{ $products->links() }}
    </div>
</div>

{{-- ── Add/Edit Product Modal ──────────────────────────────────────── --}}
<div id="product-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 z-10 animate-slide-in overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 id="modal-title" class="text-base font-bold text-gray-800">Add New Product</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="product-form" method="POST" action="{{ route('products.store') }}">
            @csrf
            <div id="method-container"></div> {{-- For @method('PUT') --}}

            <div class="p-6 grid grid-cols-2 gap-4">
                {{-- Name --}}
                <div class="col-span-2 space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Product Name</label>
                    <input type="text" name="name" id="field-name" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500">
                </div>

                {{-- SKU --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">SKU / Item Code</label>
                    <input type="text" name="item_code" id="field-item_code" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500">
                </div>

                {{-- Category --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Category</label>
                    <select name="category_id" id="field-category_id" required
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 bg-white">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Price --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Price (Rs.)</label>
                    <input type="number" step="0.01" name="price" id="field-price" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500">
                </div>

                {{-- Stock --}}
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Stock Quantity</label>
                    <input type="number" name="stock_qty" id="field-stock_qty" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500">
                </div>

                {{-- Description --}}
                <div class="col-span-2 space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Description</label>
                    <textarea name="description" id="field-description" rows="2"
                              class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500"></textarea>
                </div>

                {{-- Is Active --}}
                <div class="col-span-2 flex items-center gap-2 mt-2">
                    <input type="checkbox" name="is_active" id="field-is_active" value="1" checked
                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="field-is_active" class="text-sm text-gray-600">Product is active and visible in POS</label>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-700">Cancel</button>
                <button type="submit" id="submit-btn" class="px-6 py-2 rounded-xl text-sm font-bold text-white shadow-lg shadow-indigo-100" style="background: linear-gradient(135deg, #4f46e5, #6366f1)">
                    Create Product
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const modal = document.getElementById('product-modal');
    const form = document.getElementById('product-form');
    const title = document.getElementById('modal-title');
    const submitBtn = document.getElementById('submit-btn');
    const methodContainer = document.getElementById('method-container');

    function openAddModal() {
        title.innerText = 'Add New Product';
        submitBtn.innerText = 'Create Product';
        form.action = "{{ route('products.store') }}";
        methodContainer.innerHTML = '';
        form.reset();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function openEditModal(product) {
        title.innerText = 'Edit Product: ' + product.name;
        submitBtn.innerText = 'Update Product';
        form.action = `/products/${product.id}`;
        methodContainer.innerHTML = '@method("PUT")';

        document.getElementById('field-name').value = product.name;
        document.getElementById('field-item_code').value = product.sku || product.item_code;
        document.getElementById('field-category_id').value = product.category_id;
        document.getElementById('field-price').value = product.price;
        document.getElementById('field-stock_qty').value = product.stock_qty;
        document.getElementById('field-description').value = product.description || '';
        document.getElementById('field-is_active').checked = !!product.is_active;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Escape listener
    document.addEventListener('keydown', e => { if(e.key === 'Escape') closeModal(); });
</script>
@endsection
