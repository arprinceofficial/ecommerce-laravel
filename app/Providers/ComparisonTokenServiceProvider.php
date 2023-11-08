<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ComparisonTokenServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $comparisonToken = request()->cookie('comparison_token');

        if (!$comparisonToken) {
            // $token = Str::random(32); // Generate a random token
            // // Set the token in a cookie with a 24-hour expiration time
            // cookie()->queue(cookie('comparison_token', $token, 1440)); // 1440 minutes = 24 hours

            // Create a comparisonToken and store it in a cookie for 1 day
            $comparisonToken = md5(uniqid(rand(), true));
            setcookie('comparison_token', $comparisonToken, time() + (86400 * 30), "/");
        }
        // store comparisonToken in session
        session()->put('comparison_token', $comparisonToken);
        session()->put('productComparison', []);    }
}
