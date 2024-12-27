<?php

namespace App\Http\Controllers;

use App\Models\Fornecedor;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class FornecedorController extends Controller
{
    /* LISTAR TODOS FORNECEDORES */
    public function index(Request $request)
    {   
        $query = Fornecedor::query();
        
        /* FILTRO DE BUSCA COM QUERY STRING POR CAMPO E VALOR
        (EXEMPLO: ?cnpj_cpf=1&nome=a&email=a&endereco=a) 
        */
        $termos = $request->only('cnpj_cpf', 'nome', 'email', 'endereco');
        foreach($termos as $nome => $valor){
            if($valor){
                $query->where($nome, 'LIKE', '%' . $valor . '%');
            }
        }

        /* FILTRO DE ORDENAÇÃO COM CAMPO E TYPO(ASC, DESC)
        (EXEMPLO: ?orderby=nome&type=asc) 
        */
        if($request->has('orderby') and (in_array($request->has('orderby'),['cnpj_cpf','nome','email', 'endereco']))){
            if($request->has('type') and (in_array($request->has('type'),['asc','desc']))){
                $query->orderBy($request['orderby'], $request['type']);
            }
            
        }
        
        $fornecedoresAll = $query->paginate(2);
        return response()->json([
            'status' => true,
            'mensagem' => 'Listado com sucesso.',
            'dados' => $fornecedoresAll], 200 );
    }

    /* BUSCAR FORNECEDOR ESPECIFICO PELO ID */
    public function show(string $id)
    {
        $fornecedorOne = Fornecedor::find($id);
    
        return response()->json([
            'status' => true,
            'mensagem' => 'Listado com sucesso.',
            'dados' => $fornecedorOne], 200);
    }

    /* CRIAR NOVO FORNECEDOR */
    public function store(Request $request)
    { 
        $validated = $request->validate([
            'cnpj_cpf' => 'required|unique:fornecedores|string|max:255', /* UNICO */
            'nome' => 'required|string|max:255',
            'email' => 'required|email|string|max:255',
            'endereco' => 'nullable|string|max:255',
        ]);

        $resultado = $this->validacaoCnpjCpf($validated['cnpj_cpf']);
        if (!$resultado['status']) {
            return response()->json([
                'status' => false,
                'mensagem' => $resultado['mensagem']], 422);
        }

        try {
            $fornecedor = Fornecedor::create($request->all());
            return response()->json([
                'status' => true,
                'mensagem' => 'Cadastrado com sucesso.',
                'dados' => $fornecedor], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'mensagem' => 'Erro ao cadastrar o fornecedor.',
            ], 500);
        } 
    }

    /* ATUALIZAR FORNECEDOR ESPECIFICO PELO ID */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'cnpj_cpf' => 'required|string|max:255',
            'nome' => 'required|string|max:255',
            'email' => 'required|email|string|max:255',
            'endereco' => 'nullable|string|max:255',
        ]);

        $resultado = $this->validacaoCnpjCpf($validated['cnpj_cpf']);
        if (!$resultado['status']) {
            return response()->json([
                'status' => false,
                'mensagem' => $resultado['mensagem']], 422);
        }

        $fornecedor = Fornecedor::find($id);
        if(!$fornecedor){
            return response()->json([
                'status' => false,
                'mensagem' => 'Fornecedor não encontrado.',
            ], 404);  
        }
        try {
            $fornecedor->update($request->all());
            return response()->json([
                'status' => true,
                'mensagem' => 'Atualizado com sucesso.',
                'dados' => $fornecedor], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'mensagem' => 'Erro ao atualizar o fornecedor.',
            ], 500);
        }
    }

    /* EXCLUIR FORNECEDOR ESPECIFICO PELO ID */
    public function destroy(string $id)
    {
        $fornecedor = Fornecedor::find($id);

        if (!$fornecedor) {
            return response()->json([
                'status' => false,
                'menssagem' => 'Fornecedor não encontado.',
            ], 404);
        }

        try {
            $fornecedor->delete();
            return response()->json([
                'status' => true,
                'menssagem' => 'Fornecedor excluído com sucesso.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'mensagem' => 'Erro ao excluir o fornecedor.',
            ], 500);
        }
    }

    /* FUNCOES EXTRAS = HELPERS */

    /* FUNCAO EXTRA PARA TESTAR SE É CNPJ OU CPF */
    public function validacaoCnpjCpf(string $cnpjCpf)
    {
        if(strlen($cnpjCpf) === 14){
            $retorno = $this->validacaoCnpj($cnpjCpf);
            if($retorno['status']){
                return [
                    'status' => false,
                    'mensagem' => $retorno['mensagem']
                ];
            }
        } else if(strlen($cnpjCpf) === 11){
            $retorno = $this->validacaoCpf($cnpjCpf);
            if($retorno['status']){
                return [
                    'status' => false,
                    'mensagem' => $retorno['mensagem']
                ];
            } 
        } else{
            return [
                'status' => false,
                'mensagem' => 'CNPJ / CPF invalido.',
            ];
        }

        return [
            'status' => true,
            'mensagem' => 'CNPJ / CPF validado',
        ];
    }

    /* FUNCAO EXTRA PARA VALIDAR O CNPJ NA BRASIL API */
    public function validacaoCnpj(string $cnpj)
    {
        try {
            $client = new Client();
            $retorno = $client->get("https://brasilapi.com.br/api/cnpj/v1/{$cnpj}");

            if ($retorno->getStatusCode() === 200) {
                return ['status' => true];
            }
        } catch (\Exception $e) {
            return [
                'status' => false,
                'mensagem' => 'CNPJ não encontrado na BrasilAPI.',
            ];
        }

        return [
            'status' => false,
            'message' => 'Erro ao validar o CNPJ.',
        ];
    }

    /* FUNCAO EXTRA PARA VALIDAR O CPF NA API WEB */
    public function validacaoCpf(string $cpf)
    {
        try {
            $client = new Client();
            $retorno = $client->get("https://api.invertexto.com/v1/validator?value={$cpf}&type=cpf");

            if ($retorno->getStatusCode() === 200) {
                return ['status' => true];
            }
        } catch (\Exception $e) {
            return [
                'status' => false,
                'mensagem' => 'CPF não encontrado na API.',
            ];
        }

        return [
            'status' => false,
            'message' => 'Erro ao validar o CPF.',
        ];
    }
}
