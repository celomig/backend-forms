# Teste Backend para Cadastro de Formulário Dinâmico

## Descrição

Este projeto foi desenvolvido para o backend de um formulário dinâmico, com regras específicas de validação e armazenamento. O objetivo é permitir a criação e o gerenciamento de formulários, com dados validados por meio de uma API RESTful.

## Tecnologias Utilizadas

- **Laravel**: Framework PHP para construção de APIs e lógica de backend.
- **MySQL**: Banco de dados relacional.
- **Docker**: Contêineres para facilitar a configuração e o gerenciamento do ambiente de desenvolvimento.

## Instalação

1. Execute o container Docker:
   ```bash
   docker-compose up -d
    ```
2. A aplicação estará disponível em [http://localhost:8080](http://localhost:8080).

## Testes

### Usando o Postman

A API está disponível no seguinte endpoint base:  
[http://localhost:8080/api/v1/](http://localhost:8080/api/v1/)  
Foi utilizado o prefixo `v1` para possibilitar o versionamento futuro da API.

#### Endpoints:

- **POST /formularios/form-1/preenchimentos**

  Payload de exemplo:
  ```json
  {
    "fields": [
      {
        "id": "field-1-1",
        "value": "João Maria"
      },
      {
        "id": "field-1-3",
        "value": "Não"
      }
    ]
  }
  ```

  *Obs.: O arquivo `forms_definition.json` é utilizado para formatação e validação dos campos.*

### Executando os Testes Artisan Laravel

1. Acesse o container Docker:
   ```bash
   docker exec -it laravel_app bash
   ```

2. Execute os testes do Laravel:
   ```bash
   php artisan test
   ```
