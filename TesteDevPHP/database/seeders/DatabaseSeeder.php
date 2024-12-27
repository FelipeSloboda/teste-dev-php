<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fornecedor;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // EXECUTA O SEEDERS P/ FORNECEDOR
        $this->call([
            FornecedorSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
