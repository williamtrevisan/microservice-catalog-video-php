<?php

namespace App\Providers;

use App\Events\VideoEvent;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Eloquent\VideoEloquentRepository;
use App\Repositories\Transaction\DatabaseTransaction;
use App\Services\Storage\FileStorage;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Interface\EventDispatcherInterface;
use Core\UseCase\Interface\FileStorageInterface;
use Core\UseCase\Interface\TransactionInterface;
use Illuminate\Support\ServiceProvider;

class CleanArchitectureServiceProvider extends ServiceProvider
{
    /**
     * Register services.
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
        $this->app->singleton(
            VideoRepositoryInterface::class,
            VideoEloquentRepository::class,
        );

        $this->app->singleton(
            FileStorageInterface::class,
            FileStorage::class,
        );
        $this->app->singleton(
            EventDispatcherInterface::class,
            VideoEvent::class,
        );

        $this->app->bind(
            TransactionInterface::class,
            DatabaseTransaction::class,
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
