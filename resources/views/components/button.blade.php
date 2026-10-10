<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-primary border border-transparent rounded-xl font-bold text-sm text-white hover:bg-secondary active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 focus:ring-offset-primary-50 transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg shadow-md w-full disabled:opacity-50 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
