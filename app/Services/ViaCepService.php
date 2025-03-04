<?php

namespace App\Services;

use App\Repositories\ViaCepRepository;
use Exception;
use Illuminate\Support\Facades\Log;

class ViaCepService
{
    protected $viaCepRepository;

    public function __construct(ViaCepRepository $viaCepRepository)
    {
        $this->viaCepRepository = $viaCepRepository;
    }

    public function getAddressByZipCode(string $zipCode): ?array
    {
        try {
            $zipCode = preg_replace('/[^0-9]/', '', $zipCode);
            $address =  $this->viaCepRepository->getAddressByZipCode($zipCode);
            if (isset($address['erro']) && $address['erro']) {
                return null;
            }
            return [
                'zip_code'     => $address['cep'],
                'address'      => $address['logradouro'],
                'neighborhood' => $address['bairro'],
                'city'         => $address['localidade'],
                'state'        => $address['uf'],
                'complement'   => $address['complemento']
            ];
        } catch (Exception $e) {
            Log::error("Falha buscar endereço por cep: {$zipCode} - " . $e->getMessage());
            throw new Exception('Falha ao buscar endereço por cep.' . $e->getMessage());
        }
    }

    public function findSuggestionsAddresses(string $state, string $city, string $address): ?array
    {
        try {
            $address = str_replace(" ", "+", $address);
            $suggestions = $this->viaCepRepository->findSuggestionsAddresses($state, $city, $address);
            
            if (!$suggestions) {
                return null;
            }

            return collect($suggestions)->map(function ($item) {
                return [
                    'zip_code'     => $item['cep'],
                    'address'      => $item['logradouro'],
                    'neighborhood' => $item['bairro'],
                    'city'         => $item['localidade'],
                    'state'        => $item['uf'],
                    'complement'   => $item['complemento']
                ];
            })->toArray();
        } catch (Exception $e) {
            Log::error("Falha buscar endereço: " . $e->getMessage());
            throw new Exception('Falha ao buscar endereço.' . $e->getMessage());
        }
    }
}
