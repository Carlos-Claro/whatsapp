<?php
namespace App\Services\Whatsapp\Utils;

use Illuminate\Support\Facades\Http;

trait Curl {
    public function send(
        string $type,
        array $data,
        ): object
    {

        return $$type($data);
    }

    private static function downloadMedia( array $data): Http {
        $url = env('WHATSAPP_URL'). $data['mediaID'];
        $MediaURL = Http::withToken(env('WHATSAPP_TOKEN'))->accept('application/json')->get($url)->json();
        $response = Http::withToken(env('WHATSAPP_TOKEN'))->get($MediaURL->url);
        return $response;
    }


}

