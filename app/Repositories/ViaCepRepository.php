<?php

namespace App\Repositories;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ViaCepRepository
{
    public function getAddressByZipCode(string $zipCode): ?array
    {
        try {
            $response = Http::get("https://viacep.com.br/ws/{$zipCode}/json/");
            return $response->json();
        } catch (\Exception $e) {
            Log::error("Erro ao buscar CEP: {$zipCode} - " . $e->getMessage());
            return null;
        }
    }

    public function findSuggestionsAddresses(string $state, string $city, string $address): ?array
    {
        try {
            $response = Http::get("https://viacep.com.br/ws/{$state}/{$city}/{$address}/json/");
            Log::info("Buscando sugestões:". $response->body());
            return $response->json();
        } catch (\Exception $e) {
            Log::error("Erro ao buscar endereço - " . $e->getMessage());
            return null;
        }
    }

   
}
