<?php
namespace App\Filament\Resources\BankResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\BankResource;
use Illuminate\Routing\Router;


class BankApiService extends ApiService
{
    protected static string | null $resource = BankResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
