<?php

namespace App\Providers;

use App\Repositories\Eloquent\CastMemberEloquentRepository;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\DatabaseTransaction;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\Interface\TransactionInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            CastMemberRepositoryInterface::class,
            CastMemberEloquentRepository::class,
        );
        $this->app->singleton(
            CategoryRepositoryInterface::class,
            CategoryEloquentRepository::class,
        );
        $this->app->singleton(
            GenreRepositoryInterface::class,
            GenreEloquentRepository::class,
        );

        $this->app->bind(
            TransactionInterface::class,
            DatabaseTransaction::class,
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
