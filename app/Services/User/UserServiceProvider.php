<?php


namespace App\Services\User;

use App\Services\Transaction\Events\TransactionSubmitted;
use App\Services\User\Listeners\SendSmsToUser;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerListeners();
    }

    public function registerListeners(): void
    {
        Event::listen(
            TransactionSubmitted::class,
            SendSmsToUser::class
        );
    }
}
