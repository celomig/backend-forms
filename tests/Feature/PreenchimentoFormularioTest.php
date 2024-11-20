<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Helpers\JsonFileReader;
use Illuminate\Support\Facades\Storage;

class PreenchimentoFormularioTest extends TestCase
{

    public function test_store_filling_success()
    {
        // Payload ajustado conforme o formato correto
        $payload = [
            'fields' => [
                [
                    'id' => 'field-1-1',
                    'value' => 'João da Silva'
                ],
                [
                    'id' => 'field-1-3',
                    'value' => 'Não'
                ]
            ]
        ];
    
        // Envia a requisição POST para o endpoint correto
        $response = $this->json('POST', '/api/v1/formularios/form-1/preenchimentos', $payload);
    
        // Verifica se a resposta tem o status 201 e a estrutura correta
        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'filling_id']);
    }
    

    public function test_store_filling_required_field_empty()
    {
        $payload = [
            'fields' => [
                ['id' => 'field-1-1', 'value' => ''], // Campo obrigatório vazio
                ['id' => 'field-1-2', 'value' => 30]
            ]
        ];

        $response = $this->json('POST', '/api/v1/formularios/form-1/preenchimentos', $payload);

        $response->assertStatus(400)
            ->assertJsonStructure(['error', 'details']);
    }

    public function test_store_filling_invalid_form()
    {
        $payload = [
            'fields' => [
                ['id' => 'field-1', 'value' => 'Teste']
            ]
        ];

        $response = $this->json('POST', '/api/v1/formularios/form-inexistente/preenchimentos', $payload);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Formulário não encontrado no JSON.']);
    }

    public function test_index_fillings_success()
    {
        // Simular preenchimentos
        Storage::put('form-1-preenchimentos.json', json_encode([
            [
                'id' => '1',
                'fields' => [
                    ['id' => 'field-1', 'value' => 'João Silva'],
                    ['id' => 'field-2', 'value' => 30],
                    ['id' => 'field-3', 'value' => 'Sim']
                ]
            ]
        ]));

        $response = $this->json('GET', '/api/v1/formularios/form-1/preenchimentos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'form_id',
                'form_name',
                'fillings' => [
                    '*' => [
                        '*' => [
                            'field_id',
                            'label',
                            'value'
                        ]
                    ]
                ]
            ]);
    }

    public function test_index_fillings_invalid_form()
    {
        $response = $this->json('GET', '/api/v1/formularios/form-inexistente/preenchimentos');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Formulário não encontrado.']);
    }

    
}
