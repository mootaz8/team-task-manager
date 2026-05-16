<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Fix pour MySQL < 5.7.7 ou MariaDB < 10.2.2
        Schema::defaultStringLength(191);
        
        // Directive Blade personnalisée
        Blade::directive('datetime', function ($expression) {
            return "<?php echo ($expression)->format('d/m/Y H:i'); ?>";
        });
        
        Blade::directive('humanReadable', function ($expression) {
            return "<?php echo ($expression)->diffForHumans(); ?>";
        });
    }
}