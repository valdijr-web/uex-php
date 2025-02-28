<?php

namespace Tests\Unit;

use App\Http\Controllers\ContactController;
use App\Http\Requests\StoreContactRequest;
use App\Models\Contact;
use App\Models\User;
use App\Repositories\ContactRepository;
use App\Services\ContactService;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class ContactStoreTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Criando um usuário autenticado para os testes
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function testStoreContactSuccess()
    {
        $contactData = [
            'name'         => 'João da Silva',
            'cpf'          => '47527295040',
            "phone" => "87999580390",
            "zip_code" => "63024730",
            "address" => "Rua Maria Ana Pereira",
            "number" => "108",
            "neighborhood" => "São José",
            "city" => "Juazeiro do Norte",
            "state" => "CE",
            'complement'   => ''
        ];


        $response = $this->postJson('/api/contacts', $contactData);
        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Contato cadastrado com sucesso.'
            ]);

        // Verifica se o contato foi salvo no banco
        $this->assertDatabaseHas('contacts', [
            'user_id' => $this->user->id,
            'name'    => 'João da Silva'
        ]);
    }

    public function testStoreContactInvalidCpf()
    {
        $contactData = [
            'name'         => 'João da Silva',
            'cpf'          => '1000986543',
            "phone" => "87999580390",
            "zip_code" => "63024730",
            "address" => "Rua Maria Ana Pereira",
            "number" => "108",
            "neighborhood" => "São José",
            "city" => "Juazeiro do Norte",
            "state" => "CE",
            'complement'   => ''
        ];

        $response = $this->postJson('/api/contacts', $contactData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf']);
    }
}
