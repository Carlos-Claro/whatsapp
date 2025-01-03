<?php

namespace App\Listeners;

use App\Events\WhatsappReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReceiveWhatsappNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WhatsappReceived $event): void
    {
        //
    }
}
