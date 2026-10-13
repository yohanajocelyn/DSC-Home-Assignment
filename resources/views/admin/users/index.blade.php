<x-layout title="Users - DSC Admin">
    <div class="flex flex-col md:flex-row min-h-screen">
        <x-navbar active="users" class="h-full shrink-0 overflow-y-auto" />

        <main class="flex-1 p-6 sm:p-8 md:p-10 lg:p-12 overflow-x-hidden">
            <div class="max-w-6xl">
                <x-flash-success class="mb-6" />
                <x-flash-error class="mb-6" />

                <h1 class="text-2xl sm:text-3xl font-black text-primary-900 tracking-tight mb-6">
                    Users
                </h1>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <a href="{{ route('admin.users.create') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl shadow-md hover:bg-secondary hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5 shrink-0">
                        <x-heroicon-o-plus class="w-4 h-4 stroke-[2.5]" />
                        <span>Create a New User</span>
                    </a>

                    <div class="relative w-full sm:w-72 md:w-80 flex items-center">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-primary-500 z-10">
                            <x-heroicon-o-magnifying-glass class="w-4 h-4 text-primary-500" />
                        </span>
                        <input type="text"
                               id="userSearchInput"
                               placeholder="Search user by name..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-primary-300/80 bg-white/70 backdrop-blur-xs text-primary-900 text-sm placeholder-primary-400 focus:outline-none focus:ring-2 focus:ring-secondary focus:bg-white shadow-2xs transition-all duration-200" />
                    </div>
                </div>

                <div class="divide-y divide-primary-200/60 border-t border-b border-primary-200/60" id="usersList">
                    @forelse ($users as $user)
                        <div class="user-list-item py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors hover:bg-primary-100/50 rounded-xl px-3 sm:px-4"
                             data-user-name="{{ strtolower($user->name) }}">
                            
                            <div class="flex items-center gap-3 min-w-0 sm:w-1/2">
                                <div class="w-9 h-9 rounded-full bg-primary-200/70 border border-primary-300/60 flex items-center justify-center text-primary-800 shrink-0 shadow-2xs">
                                    <x-heroicon-s-user class="w-5 h-5 text-primary-700" />
                                </div>
                                <span class="text-base font-bold text-primary-900 truncate" title="{{ $user->name }}">
                                    {{ $user->name }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-6 sm:w-1/2 min-w-0">
                                <div>
                                    @php
                                        $roleName = $user->role?->name ?? 'Staff';
                                        $isAdminRole = strtolower($roleName) === 'admin';
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs sm:text-sm font-semibold {{ $isAdminRole ? 'bg-primary-100 text-secondary border border-primary-200/60' : 'bg-white/80 text-primary-700 border border-primary-200/80' }} shadow-2xs">
                                        <x-heroicon-o-shield-check class="w-4 h-4 {{ $isAdminRole ? 'text-secondary' : 'text-primary-500' }}" />
                                        <span>{{ $roleName }}</span>
                                    </span>
                                </div>

                                <div class="flex items-center justify-end gap-2 shrink-0">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-primary-900 bg-white hover:bg-primary-100 border border-primary-200 rounded-lg shadow-2xs hover:shadow-xs transition-all duration-200">
                                        <x-heroicon-o-pencil-square class="w-3.5 h-3.5 text-secondary" />
                                        <span>Edit</span>
                                    </a>
                                    @can('delete', $user)
                                        <button type="button"
                                                onclick="window.openDeleteUserModal('{{ $user->id }}', '{{ addslashes($user->name) }}')"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 rounded-lg shadow-2xs hover:shadow-xs transition-all duration-200 cursor-pointer">
                                            <x-heroicon-o-trash class="w-3.5 h-3.5 text-rose-600" />
                                            <span>Delete</span>
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-16 px-4 text-center">
                            <div class="w-16 h-16 mx-auto mb-2 rounded-2xl flex items-center justify-center text-primary-400">
                                <x-heroicon-o-users class="w-8 h-8 text-secondary" />
                            </div>
                            <p class="text-lg text-primary-600 max-w-md mx-auto mb-6 leading-relaxed">
                                There are currently no users registered.
                            </p>
                        </div>
                    @endforelse
                </div>

                <div id="noSearchResults" class="hidden py-12 text-center text-primary-500">
                    <p class="font-medium text-sm">No users match your search query.</p>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('userSearchInput');
            const userItems = document.querySelectorAll('.user-list-item');
            const noResults = document.getElementById('noSearchResults');

            if (searchInput) {
                searchInput.addEventListener('input', function (e) {
                    const query = e.target.value.toLowerCase().trim();
                    let matchCount = 0;

                    userItems.forEach(item => {
                        const name = item.getAttribute('data-user-name') || '';
                        if (name.includes(query)) {
                            item.style.display = '';
                            matchCount++;
                        } else {
                            item.style.display = 'none';
                        }
                    });

                    if (noResults) {
                        noResults.classList.toggle('hidden', matchCount > 0);
                    }
                });
            }
        });

        window.openDeleteUserModal = function (userId, userName) {
            const modal = document.getElementById('deleteUserConfirmModal');
            const form = document.getElementById('deleteUserConfirmModal_form');
            const message = document.getElementById('deleteUserConfirmModal_message');
            if (form && userId) {
                form.action = '/admin/users/' + userId;
            }
            if (message && userName) {
                message.textContent = `Are you sure you want to delete user "${userName}"? This will deactivate their account and they will no longer be able to log in.`;
            }
            if (modal) {
                modal.classList.remove('hidden');
            }
        };
    </script>

    <x-confirm-modal
        id="deleteUserConfirmModal"
        title="Delete User"
        message="Are you sure you want to delete this user? This will deactivate their account and they will no longer be able to log in."
        confirm-text="Delete User"
        cancel-text="Cancel"
        variant="danger"
        method="DELETE"
        action=""
    />
</x-layout>
