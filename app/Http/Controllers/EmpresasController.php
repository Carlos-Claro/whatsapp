<?php

namespace App\Http\Controllers;

use App\Services\Empresa\Search;
use Illuminate\Http\Request;

class EmpresasController extends Controller
{
    use Search;
    public function index(): array
    {
        return [];
    }
    public function search(Request $request): array
    {
        return $this->search_($request)->toArray();
    }
}
