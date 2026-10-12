<x-layout title="Create User - DSC Admin">
    <div class="flex flex-col md:flex-row min-h-screen">
        <x-navbar active="users" class="h-full shrink-0 overflow-y-auto" />

        <main class="flex-1 p-6 sm:p-8 md:p-10 lg:p-12 overflow-x-hidden">
            <div class="max-w-4xl">
                <x-flash-error class="mb-6" />

                <!-- Back Link -->
                <div class="ms-8 mb-4">
                    <a href="{{ route('admin.users.index') }}"
                       class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-secondary hover:text-primary-900 transition-colors">
                        <x-heroicon-o-arrow-left class="w-4 h-4" />
                        <span>Back to Users</span>
                    </a>
                </div>

                <x-user-form :roles="$roles ?? null" />
            </div>
        </main>
    </div>
</x-layout>
