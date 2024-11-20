<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class JsonFileReader
{
    /**
     * Lê e retorna o conteúdo de um arquivo JSON.
     *
     * @param string $filePath Caminho do arquivo no storage.
     * @return array|null Retorna os dados do JSON como array ou null em caso de erro.
     */
    public static function read(string $filePath): ?array
    {
        // Verifica se o arquivo existe
        if (!Storage::exists($filePath)) {
            return null;
        }

        // Lê o conteúdo do arquivo
        $content = Storage::get($filePath);

        // Decodifica o JSON
        $decoded = json_decode($content, true);

        // Verifica se houve erro na decodificação
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $decoded;
    }
}
