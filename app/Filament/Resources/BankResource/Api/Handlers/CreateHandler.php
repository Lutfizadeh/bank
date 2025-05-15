<?php
namespace App\Filament\Resources\BankResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\BankResource;
use App\Filament\Resources\BankResource\Api\Requests\CreateBankRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = BankResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Bank
     *
     * @param CreateBankRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateBankRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}