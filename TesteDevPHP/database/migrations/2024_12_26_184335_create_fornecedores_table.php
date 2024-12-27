<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fornecedores', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj_cpf')->unique(); /* UNICO */
            $table->string('nome');
            $table->string('email');
            $table->string('endereco')->nullable();
            $table->timestamps();
            $table->softDeletes(); /* SOFT DELETE */
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fornecedores');
    }
};
