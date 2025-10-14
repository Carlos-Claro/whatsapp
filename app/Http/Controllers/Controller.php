<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class Controller
{
    public function initPage() : array{
        return [
            'logo' => asset('storage/logoWhatsPow.png'),
        ];
    }
}
