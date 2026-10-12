<x-layout>
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Vertical Left Navbar: menus 'My Bookings' and 'Meeting Rooms' -->
        <x-navbar active="my-bookings" />

        <!-- Main Content Area: Automatically Open Menu is My Bookings -->
        <main class="flex-1 p-6 sm:p-8 md:p-10 lg:p-12 overflow-x-hidden">
            @include('staff.my-bookings.index', ['isIncluded' => true])
        </main>
    </div>
</x-layout>
