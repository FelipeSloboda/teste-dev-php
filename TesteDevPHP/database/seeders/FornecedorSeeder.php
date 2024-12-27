<?php

namespace Database\Seeders;

use App\Models\Fornecedor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FornecedorSeeder extends Seeder
{

    public function run(): void
    {
        // CRIA FORNECEDOR PARA TESTES
        Fornecedor::create([
            'cnpj_cpf' => '75802991000125',
            'nome' => 'fantasma',
            'email' => 'teste@mail.com',
            'endereco' => 'rua x num 0'
        ]);
    }
}
