<?php
namespace App\Repositories;

use App\Models\Contact;
use Illuminate\Support\Facades\Auth;

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
}