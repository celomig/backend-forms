<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Field;
use App\Models\FormFilling;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Helpers\JsonFileReader;

class PreenchimentoFormularioController extends Controller
{
    public function index($id_formulario)
    {
        // Verifica se o formulário existe e carrega os campos e preenchimentos
        $form = Form::with(['fillings', 'fields'])->find($id_formulario);
    
        if (!$form) {
            return response()->json(['error' => 'Formulário não encontrado.'], 404);
        }
    
        // Recupera os preenchimentos e seus respectivos campos
        $fillings = $form->fillings->map(function($filling) use ($form) {
            $fillingData = [];
            
            // Associa cada campo com o valor preenchido
            foreach ($form->fields as $field) {
                $fillingData[] = [
                    'field_id' => $field->id,
                    'label' => $field->label,
                    'value' => $filling->data[$field->id] ?? null,
                ];
            }
    
            return $fillingData;
        });
    
        return response()->json([
            'form_id' => $form->id,
            'form_name' => $form->name,
            'fillings' => $fillings,
        ], 200);
    }
    

    public function store(Request $request, $id_formulario)
    {
        // Lê o arquivo JSON com os formulários
        $forms = JsonFileReader::read('public/forms_definition.json');

        // Verifica se o formulário existe no JSON
        $formulario = collect($forms)->firstWhere('id', $id_formulario);
        if (!$formulario) {
            return response()->json(['error' => 'Formulário não encontrado no JSON.'], 404);
        }

        // Verifica se o formulário já existe no banco, e cria se necessário
        $form = Form::firstOrCreate(
            ['id' => $id_formulario],
            ['name' => $formulario['name']]
        );

        // Garante que todos os campos do formulário existam no banco
        foreach ($formulario['fields'] as $field) {
            // Converte o array de 'choices' para JSON se existir
            $choices = isset($field['choices']) ? json_encode($field['choices']) : null;

            Field::firstOrCreate(
                [
                    'id' => $field['id']
                ],
                [
                    'form_id' => $id_formulario,
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'required' => $field['required'],
                    'choices' => $choices, // Salvando como string JSON ou null
                ]
            );
        }

        // Monta as regras de validação dinamicamente
        $rules = [];
        $customMessages = [];
        foreach ($formulario['fields'] as $field) {
            $rule = $field['required'] ? 'required' : 'nullable';

            switch ($field['type']) {
                case 'number':
                    $rule .= '|numeric';
                    break;
                case 'text':
                    $rule .= '|string';
                    break;
                case 'select':
                    $rule .= '|in:' . implode(',', $field['choices']);
                    break;
            }

            $rules[$field['id']] = $rule;

            $customMessages["{$field['id']}.required"] = "O campo '{$field['label']}' é obrigatório.";
            if ($field['type'] === 'select') {
                $customMessages["{$field['id']}.in"] = "O campo '{$field['label']}' deve conter uma das opções: " . implode(', ', $field['choices']) . ".";
            }
        }

        // Transformando os dados recebidos no formato esperado para validação
        $inputData = collect($request->json('fields'))->pluck('value', 'id')->toArray();

        // Valida os dados do request
        $validator = Validator::make($inputData, $rules, $customMessages);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validação falhou.',
                'details' => $validator->errors(),
            ], 400);
        }

        // Salvar os dados do preenchimento no banco de dados
        $formFilling = FormFilling::create([
            'id' => Str::uuid(),
            'form_id' => $id_formulario,
            'data' => $validator->validated(),
        ]);

        return response()->json([
            'message' => 'Formulário salvo com sucesso.',
            'filling_id' => $formFilling->id,
        ], 201);
    }


    
}
