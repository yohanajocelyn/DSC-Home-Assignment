<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\User;
use App\Policies\BookingPolicy;
use App\Policies\MeetingRoomPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(MeetingRoom::class, MeetingRoomPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
