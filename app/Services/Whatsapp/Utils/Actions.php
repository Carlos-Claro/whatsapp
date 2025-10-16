<?php
namespace App\Services\Whatsapp\Utils;

use App\Services\Empresa\Search;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait Actions {
    use Search;
    public function verifyEmpresa($data): Collection {
        $return = ['type' => 'error', 'message' => 'Empresa não encontrada', 'data' => []];
        $data = str_replace(['.', '-', '/'], '', $data);
        $request = new Request(['empresa_cnpj' => $data]);
        $empresas = $this->search_($request);
        if ( $empresas->count() ){
            if ( $empresas->count() == 1 ){
                $this->conversation->empresa_id = $empresas->first()->id;
                $this->conversation->save();
                $return = ['type' => 'unique', 'message' => 'Empresa '.$empresas->first()->company_name.' encontrada.', 'data' => ['id' => $empresas->first()->id, 'name' => $empresas->first()->company_name]];
                return collect($return);
            }
            foreach ( $empresas as $empresa ){
                $return['data'][] = [
                    'id' => $empresa->id,
                    'name' => $empresa->company_name,
                ];
            }
            $return['type'] = 'multiple';
            $return['message'] = 'Foram encontradas '.$empresas->count().' empresas com esse CNPJ.';
        }
        return collect($return);

    }
}
