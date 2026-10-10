<x-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Logo or brand -->
        <!-- <div class="mb-8 transform transition-transform duration-500 hover:scale-105">
            <a href="/">
                <div class="w-20 h-20 bg-[var(--color-primary)] rounded-2xl shadow-xl flex items-center justify-center text-white text-3xl font-black italic">
                    A
                </div>
            </a>
        </div> -->

        <!-- Login Box -->
        <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white/80 backdrop-blur-md shadow-2xl overflow-hidden sm:rounded-3xl border border-white/40">
            
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-black text-[var(--color-primary)] mb-2">Book a Meeting Room</h2>
                <p class="text-primary-600 font-medium">Log in to your account</p>
            </div>

            <!-- Note: No action attribute provided as requested for static design -->
            <form>
                @csrf

                <!-- Email Address -->
                <div class="mb-6">
                    <x-input-label for="email" value="Email Address" />
                    <x-input id="email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" placeholder="you@example.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-2">
                        <x-input-label for="password" value="Password" class="mb-0" />
                    </div>
                    
                    <x-input id="password" class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end">
                    <x-button class="ms-3">
                        Log In
                    </x-button>
                </div>
            </form>
            
            <div class="mt-8 pt-6 border-t border-primary-200/50 text-center">
                <p class="text-sm text-primary-600 font-medium">
                    Don't have an account? Contact an admin if you think this is a mistake.
                </p>
            </div>
        </div>
    </div>
</x-layout>
