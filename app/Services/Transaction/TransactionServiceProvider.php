<?php

namespace App\Services\Transaction;

use App\Services\Utils\Transformers\Str\ToEnglishNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class TransactionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerRoutes();
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
    }

    public function boot()
    {
        $this->registerCardValidator();
        $this->registerRequestMacros();
    }

    public function registerRoutes()
    {
        Route::middleware(['web'])
            ->prefix('api/v1')
            ->group(base_path('app/Services/Transaction/Routes/routes.php'));
    }

    public function registerCardValidator(): void
    {
        Validator::extend('card_number', function ($attribute, string $value) {
            return validateCardNum($value);
        });
    }

    public function registerRequestMacros(): void
    {
        Request::macro('convertToEnCharacters', function (array $values) {
            foreach ($values as $inputKey) {
                $this->merge([$inputKey => ToEnglishNumber::convert($this->get($inputKey))]);
            }
        });
    }
}
