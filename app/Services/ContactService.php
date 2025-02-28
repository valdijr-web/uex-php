<?php

namespace App\Services;

use App\Models\Contact;
use App\Repositories\ContactRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContactService
{
    protected $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    public function createContact(array $data): ?Contact
    {
        try {
            // Latitude e longitude via busca com Google Maps API
            $geolocation = $this->getCoordinates($data['address'], $data['number'], $data['city'], $data['state']);
            if (!$geolocation) {
                return ['error' => 'Endereço não encontrado no Google Maps.'];
            }

            $data['latitude'] = $geolocation['lat'];
            $data['longitude'] = $geolocation['lng'];
            $data['user_id'] = Auth::id();

            return $this->contactRepository->create($data);
        } catch (Exception $e) {
            Log::error('Falha ao registrar um novo contato: ' . $e->getMessage(), [
                'data' => $data
            ]);
            throw new Exception('Falha ao registrar um novo contato.');
        }
    }

    public function updateContact(Contact $contact, array $data): ?Contact
    {
        try {
            return $this->contactRepository->update($contact, $data);
        } catch (Exception $e) {
            Log::error('Falha ao atualizar contato: ' . $e->getMessage(), [
                'data' => $data
            ]);
            throw new Exception('Falha ao atualizar contato.');
        }
    }

    public function deleteContact(Contact $contact): bool
    {
        try {
            if ($contact->user_id !== Auth::id()) {
                Log::warning("Tentativa de exclusão de contato não autorizado. Contato ID: {$contact->id} | Usuário ID: " . Auth::id());
                throw new Exception('Tentativa de exclusão de contato não autorizado.');
            }
            return $this->contactRepository->delete($contact);
        } catch (Exception $e) {
            Log::error('Falha ao excluir contato: ' . $e->getMessage());
            throw new Exception('Falha ao excluir contato.');
        }
    }

    public function listContacts(array $filters)
    {
        try {
            return $this->contactRepository->getContacts($filters);
        } catch (Exception $e) {
            Log::error('Falha ao listar contatos do usuário: ' . $e->getMessage());
            throw new Exception('Falha ao listar contatos do usuário.');
        }
    }

    private function getCoordinates($address, $number, $city, $state)
    {
        $fullAddress = urlencode("{$address}, {$number}, {$city} - {$state}");
        $apiKey = env('GOOGLE_MAPS_API_KEY'); // Defina a chave no .env
        $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
            'address' => $fullAddress,
            'key' => $apiKey
        ]);

        if ($response->successful() && isset($response['results'][0]['geometry']['location'])) {
            return $response['results'][0]['geometry']['location'];
        }

        return null;
    }

    public function getContactCoordinates(Contact $contact): ?array
    {
        try {
            return $this->contactRepository->getCoordinates($contact);
        } catch (Exception $e) {
            Log::error("Erro ao buscar coordenadas do contato ID {$contact->id}: " . $e->getMessage());
            throw new Exception('Falha ao buscar coordenadas do contato.');
        }
    }
}
