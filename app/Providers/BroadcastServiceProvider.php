<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Broadcast::routes([ 'middleware' => [ 'auth:broadcasting']]);
        // Broadcast::routes();
        Broadcast::routes(['prefix' => 'api', 'middleware' => 'api']);
        require base_path('routes/channels.php');
    }
}
