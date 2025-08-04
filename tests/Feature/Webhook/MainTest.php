<?php

namespace Tests\Feature\Webhook;

use App\Models\Contacts;
use App\Models\Conversations;
use App\Models\Messages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MainTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_webhook_add_message(): void
    {
        $data = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => '435867736284078',
                    'changes' => [
                        [
                            'field' => 'messages',
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'metadata' => [
                                    'display_phone_number' => env('WHATSAPP_FROM_PHONE_NUMBER_ID'),
                                    'phone_number_id' => env('WHATSAPP_FROM_PHONE_NUMBER_ID'),
                                ],
                                'contacts' => [
                                    [
                                        'profile' => [
                                            'name' => 'Test Contact2',
                                        ],
                                        'wa_id' => '12345678902',
                                    ],
                                ],
                                'messages' => [
                                    [
                                        'from' => '12345678902',
                                        'id' => 'hhhhttttpppp',
                                        'timestamp' => time(),
                                        'type' => 'text',
                                        'text' => [
                                            'body' => "Hello",
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $response = $this->postJson('/webhook', $data);
        $response->assertStatus(200);
        $message = Messages::where('wam_id', 'hhhhttttpppp')->first();
        $this->assertNotNull($message);
        $this->assertEquals('Hello', $message->body);
        Contacts::where('phone', '12345678902')->delete();
        Conversations::where('id', $message->conversation_id)->delete();
        Messages::where('wam_id', 'hhhhttttpppp')->delete();
    }
}
