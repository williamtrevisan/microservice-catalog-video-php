<?php

namespace App\Exceptions;

use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Exception\NotificationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $throwable)
    {
        if ($throwable instanceof NotFoundException) {
            return $this->showError($throwable->getMessage(), Response::HTTP_NOT_FOUND);
        }

        if ($throwable instanceof EntityValidationException) {
            return $this->showError(
                $throwable->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ($throwable instanceof NotificationException) {
            return $this->showError(
                $throwable->getMessage(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return parent::render($request, $throwable);
    }

    private function showError(string $message, int $statusCode)
    {
        return response()->json(['message' => $message], $statusCode);
    }
}
