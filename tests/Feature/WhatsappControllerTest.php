<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WhatsappControllerTest extends TestCase
{
    public function test_conversations(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/conversations');
        $response->assertStatus(200);
    }
}
