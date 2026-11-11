<?php

namespace App\Providers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
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
        if (config('app.env') === 'production' || str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        Blade::directive('ist', function ($expression) {
            return "<?php echo ({$expression}) ? ({$expression})->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') . ' IST' : ''; ?>";
        });
    }
}
