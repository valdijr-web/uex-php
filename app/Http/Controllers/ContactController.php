<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactListRequest;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use App\Services\ContactService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{

    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ContactListRequest $request): JsonResponse
    {
        try {
            $contacts = $this->contactService->listContacts($request->validated());
            return response()->json($contacts);
        } catch (Exception $e) {
            return response()->json(['message' => 'Oops! Erro interno no servidor. - ' . $e->getMessage()], 500);
        }
    }

    public function store(StoreContactRequest $request): JsonResponse
    {
        try {
            $contact = $this->contactService->createContact($request->validated());
            return response()->json([
                'message' => 'Contato cadastrado com sucesso.',
                'contact' => $contact
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Oops! Erro interno no servidor. - ' . $e->getMessage()], 500);
        }
    }

    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        try {
            $contact = $this->contactService->updateContact($contact, $request->validated());

            return response()->json([
                'message' => 'Contato atualizado com sucesso.',
                'contact' => $contact
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Oops! Erro interno no servidor. - ' .  $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        try {
            $this->contactService->deleteContact($contact);
            return response()->json(['message' => 'Contato excluído com sucesso.']);
        } catch (Exception $e) {
            return response()->json(['message' => 'Oops! Erro interno no servidor.' . $e->getMessage()], 500);
        }
    }
}
