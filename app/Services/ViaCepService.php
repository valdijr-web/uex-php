<?php

namespace App\Services;

use App\Repositories\ViaCepRepository;

class ViaCepService
{
    protected $viaCepRepository;

    public function __construct(ViaCepRepository $viaCepRepository)
    {
        $this->viaCepRepository = $viaCepRepository;
    }

    public function getAddressByZipCode(string $zipCode): ?array
    {
        $zipCode = preg_replace('/[^0-9]/', '', $zipCode);
        $address =  $this->viaCepRepository->getAddressByZipCode($zipCode);
        if (!$address) {
            return null;
        }
        return [
            'zip_code'     => $address['cep'],
            'address'      => $address['logradouro'],
            'neighborhood' => $address['bairro'],
            'city'         => $address['localidade'],
            'state'        => $address['uf']
        ];
    }

    public function findSuggestionsAddresses(string $state, string $city, string $address): ?array
    {
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
                'state'        => $item['uf']
            ];
        })->toArray();
    }
}
