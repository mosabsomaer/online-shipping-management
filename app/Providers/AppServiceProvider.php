<?php

namespace App\Providers;

use App\Events\OrderStatusChanged;
use App\Listeners\SendOrderStatusEmail;
use App\Listeners\SendWelcomeEmail;
use App\Models\Order;
use App\Observers\OrderObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Event;
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
        Order::observe(OrderObserver::class);

        Event::listen(
            OrderStatusChanged::class,
            SendOrderStatusEmail::class,
        );

        Event::listen(
            Registered::class,
            SendEmailVerificationNotification::class,
        );

        Event::listen(
            Verified::class,
            SendWelcomeEmail::class,
        );
    }
}
