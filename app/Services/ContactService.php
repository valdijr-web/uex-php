<?php

namespace App\Services;

use App\Repositories\ContactRepository;
use Illuminate\Support\Facades\Http;

class ContactService
{
    protected $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function createContact(array $data)
    {
        // Latitude e longitude via busca com Google Maps API
        $geolocation = $this->getCoordinates($data['address'], $data['number'], $data['city'], $data['state']);
        if (!$geolocation) {
            return ['error' => 'Endereço não encontrado no Google Maps.'];
        }

        $data['latitude'] = $geolocation['lat'];
        $data['longitude'] = $geolocation['lng'];
        $data['user_id'] = auth()->id();
        $data['cpf'] = preg_replace('/\D/', '', $data['cpf']); // Remover formatação do CPF

        return $this->contactRepository->create($data);
    }

    private function getCoordinates($address, $number, $city, $state)
    {
        $fullAddress = urlencode("{$address}, {$number}, {$city} - {$state}");
        $apiKey = 'AIzaSyCw1U0GTGrj0ihP4vO1sxjyc1Skra02-7g';//env('GOOGLE_MAPS_API_KEY'); // Defina sua chave no .env
        $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
            'address' => $fullAddress,
            'key' => $apiKey
        ]);

        if ($response->successful() && isset($response['results'][0]['geometry']['location'])) {
            return $response['results'][0]['geometry']['location'];
        }

        return null;
    }
}
