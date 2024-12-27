<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; /* SOFT DELETE */

class Fornecedor extends Model
{
    use SoftDeletes; /* SOFT DELETE */
    protected $table = 'fornecedores';
    protected $fillable = ['cnpj_cpf','nome','email','endereco'];

}
