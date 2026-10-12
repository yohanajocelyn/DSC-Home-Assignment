@props([
    'active' => null,
    'menus' => null,
])

@php
    $user = auth()->user();
    // Default role name and type
    $roleName = $user?->role?->name ?? 'Admin';
    $isAdmin = $user ? $user->isAdmin() : ($roleName === 'Admin');

    // Automatically detect active menu from route/URL if not explicitly specified
    if ($active === null) {
        if (request()->routeIs('*.my-bookings.*') || request()->is('staff/my-bookings*') || request()->routeIs('staff.dashboard')) {
            $active = 'my-bookings';
        } elseif (request()->routeIs('*.meeting-rooms.*') || request()->is('*/meeting-rooms*')) {
            $active = 'meeting-rooms';
        } elseif (request()->routeIs('*.bookings.*') || request()->is('*/bookings*')) {
            $active = 'bookings';
        } elseif (request()->routeIs('*.users.*') || request()->is('*/users*')) {
            $active = 'users';
        } else {
            $active = $isAdmin ? 'meeting-rooms' : 'my-bookings';
        }
    }

    if ($menus === null) {
        if ($isAdmin) {
            $menus = [
                [
                    'key' => 'meeting-rooms',
                    'label' => 'Meeting Rooms',
                    'url' => \Route::has('admin.meeting-rooms.index') ? route('admin.meeting-rooms.index') : url('/admin/meeting-rooms'),
                ],
                [
                    'key' => 'bookings',
                    'label' => 'Bookings',
                    'url' => \Route::has('admin.bookings.index') ? route('admin.bookings.index') : url('/admin/bookings'),
                ],
                [
                    'key' => 'users',
                    'label' => 'Users',
                    'url' => \Route::has('admin.users.index') ? route('admin.users.index') : url('/admin/users'),
                ],
            ];
        } else {
            $menus = [
                [
                    'key' => 'my-bookings',
                    'label' => 'My Bookings',
                    'url' => \Route::has('staff.my-bookings.index') ? route('staff.my-bookings.index') : (\Route::has('staff.dashboard') ? route('staff.dashboard') : url('/staff/bookings')),
                ],
                [
                    'key' => 'meeting-rooms',
                    'label' => 'Meeting Rooms',
                    'url' => \Route::has('staff.meeting-rooms.index') ? route('staff.meeting-rooms.index') : url('/staff/meeting-rooms'),
                ],
            ];
        }
    }
@endphp

<aside {{ $attributes->merge(['class' => 'w-full md:w-60 lg:w-72 shrink-0 md:sticky md:top-0 md:h-screen md:overflow-y-auto pl-6 sm:pl-10 md:pl-14 lg:pl-16 pr-6 md:pr-10 py-8 md:py-12 border-b md:border-b-0 md:border-r border-primary-900 flex flex-col justify-between']) }}>
    <div>
        <!-- Menu Links -->
        <nav class="space-y-4 md:space-y-6">
            @foreach ($menus as $menu)
                @php
                    $isActive = ($active === $menu['key'] 
                                || strtolower($active) === strtolower($menu['label'])
                                || ($menu['key'] === 'my-bookings' && in_array(strtolower($active), ['my-bookings', 'bookings', 'my bookings', 'dashboard']))
                                || ($menu['key'] === 'meeting-rooms' && in_array(strtolower($active), ['meeting-rooms', 'meeting rooms'])));
                @endphp
                <div>
                    <a href="{{ $menu['url'] }}"
                       class="text-base sm:text-lg block tracking-wide transition-all duration-200 {{ $isActive ? 'text-primary-900 font-bold underline decoration-2 underline-offset-8' : 'text-primary-800/80 font-medium hover:text-primary-900 hover:underline hover:decoration-primary-400 hover:underline-offset-8' }}">
                        {{ $menu['label'] }}
                    </a>
                </div>
            @endforeach
        </nav>
    </div>

    <!-- User Profile Section -->
    <div class="mt-12 md:mt-24 pt-6">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-11 h-11 rounded-full bg-primary-200/70 border border-primary-300/60 flex items-center justify-center text-primary-800 shrink-0 shadow-2xs">
                    <x-heroicon-s-user class="w-6 h-6 text-primary-700" />
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-primary-900 truncate">
                        {{ $user->name ?? 'Name' }}
                    </p>
                    <p class="text-xs text-primary-600 font-medium">
                        {{ $roleName }}
                    </p>
                </div>
            </div>

            @auth
                <!-- Logout trigger button -->
                <button type="button"
                        id="navbarLogoutBtn"
                        onclick="window.openLogoutModal()"
                        class="p-1.5 text-primary-500 hover:text-primary-900 hover:bg-primary-100 rounded-lg transition-colors cursor-pointer shrink-0"
                        title="Log Out"
                        aria-label="Log Out">
                    <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4" />
                </button>
            @endauth
        </div>
    </div>
</aside>

@auth
    <!-- Logout Confirmation Modal Component -->
    <x-confirm-modal
        id="logoutConfirmModal"
        title="Log Out"
        message="Are you sure you want to log out of your account?"
        confirm-text="Log Out"
        cancel-text="Cancel"
        variant="danger"
        method="POST"
        :action="route('logout')"
    />

    <script>
        window.openLogoutModal = function () {
            const modal = document.getElementById('logoutConfirmModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        };

        window.closeLogoutModal = function () {
            const modal = document.getElementById('logoutConfirmModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        };
    </script>
@endauth
