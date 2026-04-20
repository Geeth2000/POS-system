@extends('layouts.dashboard')

@section('title', 'Staff Management')
@section('page-title', 'Staff Management')
@section('page-subtitle', 'Add, manage, and control access for your team')

@section('styles')
<style>
    .modal-backdrop { backdrop-filter: blur(4px); }
    .modal-enter { animation: modalIn 0.25s ease-out; }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(-10px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
</style>
@endsection

@section('content')

{{-- ── Page Header + Add Button ──────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-sm text-gray-500">
            @if(Auth::user()->isAdmin())
                Showing all staff members
            @else
                Showing cashiers you manage
            @endif
        </p>
    </div>
    <button id="open-modal-btn"
        class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition-all"
        style="background: linear-gradient(135deg, #4f46e5, #6366f1);"
        onmouseover="this.style.background='linear-gradient(135deg, #4338ca, #4f46e5)'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 8px 25px rgba(79,70,229,0.35)'"
        onmouseout="this.style.background='linear-gradient(135deg, #4f46e5, #6366f1)'; this.style.transform=''; this.style.boxShadow=''">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Staff Member
    </button>
</div>

{{-- ── Staff Table ────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Staff Member</th>
                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined</th>
                <th class="text-right px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50 transition-colors group">
                {{-- Name + Email --}}
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                             style="background: linear-gradient(135deg,
                                {{ $user->isAdmin() ? '#7c3aed, #4f46e5' : ($user->isManager() ? '#2563eb, #0891b2' : '#059669, #10b981') }})">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $user->email }}</p>
                        </div>
                    </div>
                </td>

                {{-- Role Badge --}}
                <td class="px-6 py-4">
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold capitalize
                        {{ $user->isAdmin() ? 'bg-purple-100 text-purple-700' : ($user->isManager() ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                        {{ $user->role }}
                    </span>
                </td>

                {{-- Status --}}
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-green-400' : 'bg-gray-300' }}"></div>
                        <span class="text-xs text-gray-600">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                </td>

                {{-- Date --}}
                <td class="px-6 py-4 text-xs text-gray-400">
                    {{ $user->created_at->format('M j, Y') }}
                </td>

                {{-- Actions --}}
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">

                        {{-- Toggle Status --}}
                        <form method="POST" action="{{ route('users.toggle-status', $user) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}"
                                class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors">
                                @if($user->is_active)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </button>
                        </form>

                        {{-- Delete (Admin only, non-admin targets) --}}
                        @if(Auth::user()->isAdmin() && !$user->isAdmin())
                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                              onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                title="Delete user"
                                class="p-2 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-600">No staff members yet</p>
                        <p class="text-xs text-gray-400">Click "Add Staff Member" to get started</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ── Add User Modal ─────────────────────────────────────────────────── --}}
<div id="add-user-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
    {{-- Backdrop --}}
    <div id="modal-backdrop"
         class="absolute inset-0 bg-gray-900/50 modal-backdrop cursor-pointer"
         onclick="closeModal()"></div>

    {{-- Modal Card --}}
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 modal-enter z-10">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Add Staff Member</h2>
                <p class="text-xs text-gray-400 mt-0.5">Fill in the details to create a new account</p>
            </div>
            <button onclick="closeModal()" class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('users.store') }}" id="add-user-form">
            @csrf
            <div class="px-6 py-5 space-y-4">

                {{-- Validation Errors --}}
                @if($errors->any())
                <div class="px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600">
                    <ul class="space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Name --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="John Smith"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>

                {{-- Email --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="john@example.com"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>

                {{-- Phone (optional) --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Phone <span class="text-gray-400 font-normal normal-case">(optional)</span></label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+94 77 123 4567"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>

                {{-- Role --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</label>
                    <select name="role" required
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white">
                        <option value="">Select a role…</option>
                        @if(Auth::user()->isAdmin())
                            <option value="admin"   {{ old('role') === 'admin'   ? 'selected' : '' }}>Admin</option>
                            <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                        @endif
                        <option value="cashier" {{ old('role') === 'cashier' ? 'selected' : '' }}>Cashier</option>
                    </select>
                </div>

                {{-- Password --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Password</label>
                    <input type="password" name="password" required placeholder="Min. 8 characters"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>

                {{-- Confirm Password --}}
                <div class="space-y-1">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Confirm Password</label>
                    <input type="password" name="password_confirmation" required placeholder="Repeat password"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all">
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="modal-submit-btn"
                    class="px-5 py-2 text-sm font-semibold text-white rounded-xl transition-all"
                    style="background: linear-gradient(135deg, #4f46e5, #6366f1)">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const modal = document.getElementById('add-user-modal');

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.getElementById('open-modal-btn').addEventListener('click', openModal);

    // Auto-open if validation errors exist (re-show modal after failed submit)
    @if($errors->any())
        openModal();
    @endif

    // Auto-open from anchor link
    if (window.location.hash === '#add-user') openModal();

    // Loading state
    document.getElementById('add-user-form').addEventListener('submit', function() {
        const btn = document.getElementById('modal-submit-btn');
        btn.disabled = true;
        btn.textContent = 'Creating…';
        btn.style.opacity = '0.8';
    });

    // ESC to close
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endsection
