<x-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center py-6 sm:py-12">

        <!-- Login Box -->
        <div class="w-full sm:max-w-md px-6 py-8 sm:px-8 sm:py-10 bg-white/80 backdrop-blur-md shadow-2xl overflow-hidden sm:rounded-3xl border border-white/40">
            
            <div class="mb-6 sm:mb-8 text-center">
                <h2 class="text-2xl sm:text-3xl font-black text-[var(--color-primary)] mb-2 leading-tight">Book a Meeting Room</h2>
                <p class="text-sm sm:text-base text-primary-600 font-medium">Log in to your account</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-5 sm:mb-6">
                    <x-input-label for="email" value="Email Address" />
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mb-6 sm:mb-8">
                    <div class="flex justify-between items-center mb-2">
                        <x-input-label for="password" value="Password" class="mb-0" />
                    </div>
                    
                    <x-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end w-full">
                    <x-button class="w-full">
                        Log In
                    </x-button>
                </div>
            </form>
            
            <div class="mt-6 sm:mt-8 pt-6 border-t border-primary-200/50 text-center">
                <p class="text-xs sm:text-sm text-primary-600 font-medium leading-relaxed">
                    Don't have an account? <br class="sm:hidden" />Contact an admin if you think there's been a mistake.
                </p>
            </div>
        </div>
    </div>
</x-layout>
