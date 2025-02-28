<?php

namespace App\Repositories;

use App\Models\Contact;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContactRepository
{
    public function create(array $data): Contact
    {
        $contact = new Contact();
        $contact->user_id = Auth::id();
        $contact->name = $data['name'];
        $contact->cpf = $data['cpf'];
        $contact->phone = $data['phone'];
        $contact->zip_code = $data['zip_code'];
        $contact->address = $data['address'];
        $contact->number = $data['number'];
        $contact->neighborhood = $data['neighborhood'];
        $contact->city = $data['city'];
        $contact->state = $data['state'];
        $contact->complement = $data['complement'];
        $contact->save();
        return $contact;
    }

    public function findById($id): Contact
    {
        return Contact::where('user_id', Auth::id())->findOrFail($id);
    }

    public function update(Contact $contact, array $data): ?Contact
    {
        $contact->name = $data['name'];
        $contact->cpf = $data['cpf'];
        $contact->phone = $data['phone'];
        $contact->zip_code = $data['zip_code'];
        $contact->address = $data['address'];
        $contact->number = $data['number'];
        $contact->neighborhood = $data['neighborhood'];
        $contact->city = $data['city'];
        $contact->state = $data['state'];
        $contact->complement = $data['complement'];
        $contact->save();
        return $contact;
    }

    public function delete(Contact $contact): bool
    {
        return $contact->delete();
    }

    public function getContacts(array $filters)
    {
        $query = Contact::where('user_id', Auth::id());

        if (!empty($filters['name'])) {
            $query->where('name', 'LIKE', "%{$filters['name']}%");
        }

        if (!empty($filters['cpf'])) {
            $query->where('cpf', $filters['cpf']);
        }

        return $query->orderBy('name', 'asc')
            ->paginate(
                $filters['limit'] ?? 10, // Número de itens por página
                ['*'], // Seleciona todas as colunas
                'page', // Nome do parâmetro da página
                $filters['page'] ?? 1 // Página atual (padrão: 1)
            );
    }
}
