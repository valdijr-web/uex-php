<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\ContactRepository;
use App\Services\ContactService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //Criando o Binding de Dependências
        $this->app->singleton(ContactRepository::class, function () {
            return new ContactRepository();
        });

        $this->app->singleton(ContactService::class, function ($app) {
            return new ContactService($app->make(ContactRepository::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return 'https://uex.com/reset-password?token='.$token.'&email='.$user->getEmailForPasswordReset();
        });
    }
}
