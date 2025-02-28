<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ViaCepRepository
{
    public function getAddressByZipCode(string $zipCode): ?array
    {
        $response = Http::get("https://viacep.com.br/ws/{$zipCode}/json/");
        return $response->json();
    }

    public function findSuggestionsAddresses(string $state, string $city, string $address): ?array
    {
        $response = Http::get("https://viacep.com.br/ws/{$state}/{$city}/{$address}/json/");
        return $response->json();
    }
}
