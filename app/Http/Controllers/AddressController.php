<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuggestionsAddressRequest;
use App\Http\Requests\ZipCodeRequest;
use App\Services\ViaCepService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    protected $viaCepService;

    public function __construct(ViaCepService $viaCepService)
    {
        $this->viaCepService = $viaCepService;
    }

    public function getAddressByZipCode(ZipCodeRequest $request): JsonResponse
    {
        try {
            $zipCode = $request->input('zip_code');
            $address = $this->viaCepService->getAddressByZipCode($zipCode);

            if ($address === null) {
                return response()->json(['message' => 'CEP não encontrado.'], 404);
            }

            return response()->json($address);
        } catch (Exception $e) {
            return response()->json(['message' => 'Oops! Erro interno no servidor.'. $e->getMessage()], 500);
        }
    }

    public function getSuggestionsAddress(SuggestionsAddressRequest $request): JsonResponse
    {
        try {
            $state = $request->input('state');
            $city = $request->input('city');
            $address = $request->input('address');

            $suggestions = $this->viaCepService->findSuggestionsAddresses($state, $city, $address);
            if (!$suggestions) {
                return response()->json(['message' => 'Nenhum endereço encontrado.'], 404);
            }

            return response()->json($suggestions);
        } catch (Exception $e) {
            Log::error("Erro ao buscar sugestões de endereço: " . $e->getMessage());
            return response()->json(['message' => 'Oops! Erro interno no servidor.'], 500);
        }
    }
}
