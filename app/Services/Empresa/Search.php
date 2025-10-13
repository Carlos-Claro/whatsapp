<?php
namespace App\Services\Empresa;

use App\Models\Empresas;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

trait Search
{
    public function search_(Request $request) : Collection
    {
        $empresa = $this->getEmpresa($request);
        return $empresa;
    }
    private function getEmpresa(Request $request)
    {
        $search = Empresas::select(
                                'empresas.id as id',
                                'empresas.empresa_nome_fantasia as name',
                                'empresas.empresa_razao_social as company_name',
                                'empresas.empresa_cnpj as document',
                                'empresas.whatsapp as social',
                                'empresas.empresa_telefone as fone',
                                'empresas.empresa_email as email',
                            );
        foreach($this->filters as $key => $value) {
            if ($request->has($key)) {
                $search->where(
                                $value['where']['field'],
                                $value['where']['type'],
                                $value['where']['type'] == 'like' ? '%'.$request->{$key}.'%' : $request->{$key}
                            );
            }
        }
        return $search->get();

    }
    protected $filters = [
        'id' => ['title' => 'ID', 'where' => ['field' => 'empresas.id', 'type' => '=',]],
        'empresa_nome_fantasia' => ['title' => 'Nome Fantasia', 'where' => ['field' => 'empresas.empresa_nome_fantasia', 'type' => 'like',]],
        'empresa_razao_social' => ['title' => 'Razão Social', 'where' => ['field' => 'empresas.empresa_razao_social', 'type' => 'like',]],
        'empresa_cnpj' => ['title' => 'CNPJ', 'where' => ['field' => 'empresas.empresa_cnpj', 'type' => '=',]],
        'whatsapp' => ['title' => 'Numero Social', 'where' => ['field' => 'empresas.whatsapp', 'type' => '=',]],
        'empresa_telefone' => ['title' => 'Numero Telefone', 'where' => ['field' => 'empresas.empresa_telefone', 'type' => '=',]],
        'empresa_email' => ['title' => 'Email', 'where' => ['field' => 'empresas.empresa_email', 'type' => 'like',]],
        'search' => ['title' => 'Pesquisa', 'where' => ['field' => 'empresas.empresa_nome_fantasia', 'type' => 'like',]],
    ];

}
