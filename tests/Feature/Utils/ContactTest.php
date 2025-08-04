<?php

namespace Tests\Feature\Utils;

use App\Models\Contacts;
use App\Services\Whatsapp\Utils\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use Contact;
    /**
     * A basic feature test example.
     */
    public function test_contact_created(): void
    {
        $contactData = [
            'phone_id' => '1234567890',
            'name' => 'Test Contact',
            'email' => 'test@wp.com.br',
            'phone' => '1234567890',
        ];
        $contact = $this->existsContact($contactData);
        $this->assertNotNull($contact);
        $this->assertEquals($contactData['phone_id'], $contact->phone_id);
    }
    public function test_another_contact_created_and_return_right_contact(): void
    {
        Contacts::where('phone_id', '12541544111')->delete();
        $contactData = [
            'phone_id' => '12541544111',
            'name' => 'Contact 2',
            'phone' => '12541544111',
        ];
        $contact = $this->existsContact($contactData);
        $this->assertNotNull($contact);
        $this->assertEquals($contactData['phone_id'], $contact->phone_id);
    }
}
