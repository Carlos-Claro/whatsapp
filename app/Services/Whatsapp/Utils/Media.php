<?php
namespace App\Services\Whatsapp\Utils;

use Illuminate\Support\Facades\Http;

trait Media {
    public function downloadMedia( $mediaId ) {
        $url = env('WHATSAPP_URL'). $mediaId;
        $MediaURL = Http::withToken(env('WHATSAPP_TOKEN'))->accept('application/json')->get($url)->json();
        $response = Http::withToken(env('WHATSAPP_TOKEN'))->get($MediaURL->url);
        return $response;
    }


}

