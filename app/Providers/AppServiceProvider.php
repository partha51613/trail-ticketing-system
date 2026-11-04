<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        Blade::directive('ist', function ($expression) {
            return "<?php echo ({$expression}) ? ({$expression})->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') . ' IST' : ''; ?>";
        });
    }
}
