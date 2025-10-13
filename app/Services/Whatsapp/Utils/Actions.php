<?php
namespace App\Services\Whatsapp\Utils;

use App\Services\Empresa\Search;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

trait Actions {
    use Search;
    public function verifyEmpresa($data) {
        $data = str_replace(['.', '-', '/'], '', $data);
        $request = new Request(['empresa_cnpj' => $data]);
        $empresas = $this->search_($request);
        if ( $empresas->count() ){
            $return = [];
            foreach ( $empresas as $empresa ){
                $return[] = [
                    'id' => $empresa->id,
                    'name' => $empresa->company_name,
                ];
            }
            return collect($return);
        }
        return false;

    }
}
