<x-layout>
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Vertical Left Navbar (Not flush with edge, separated by thin line) -->
        <x-navbar active="meeting-rooms" />

        <!-- Main Content Area: Automatically Open Menu is Meeting Rooms -->
        <main class="flex-1 p-6 sm:p-8 md:p-10 lg:p-12 overflow-x-hidden">
            @include('admin.meeting-rooms.index')
        </main>
    </div>
</x-layout>
