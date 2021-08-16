<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Company;
use App\Models\Contact;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @return void
     */
    /*
    public function test_unauthenticated_users_cannot_access_contact_api_endpoints()
    {
        $index = $this->json('GET', '/api/contacts');
        $index->assertStatus(401);

        $store = $this->json('POST', '/api/contacts');
        $store->assertStatus(401);

        $show = $this->json('GET', '/api/contacts/0');
        $show->assertStatus(401);

        $update = $this->json('PUT', '/api/contacts/0');
        $update->assertStatus(401);

        $destroy = $this->json('DELETE', '/api/contacts/0');
        $destroy->assertStatus(401);
    }
    */

    /**
     * @test
     * 
     * @return void
     */
    public function test_can_create_a_contact()
    {
        $faker = Factory::create();

        $user = User::factory()->create();

        $company = Company::factory()->create();

        /*
        $response = $this->actingAs($user, 'api')->json('POST', '/api/contacts', [
            'company_id'   => $company->id,
            'name'         => $name = $faker->name,
            'phone_number' => $phoneNumber = $faker->phoneNumber,
        ]);
        */
        $response = $this->json('POST', '/api/contacts', [
            'company_id'   => $company->id,
            'name'         => $name = $faker->name,
            'phone_number' => $phoneNumber = $faker->phoneNumber,
        ]);

        $response->assertJsonStructure([
            'id', 'company_id', 'name', 'phone_number', 'created_at', 'updated_at',
        ])->assertJson([
            'company_id'   => $company->id,
            'name'         => $name,
            'phone_number' => $phoneNumber,
        ])->assertStatus(201);

        $this->assertDatabaseHas('contacts', [
            'company_id'   => $company->id,
            'name'         => $name,
            'phone_number' => $phoneNumber,
        ]);
    }

    /**
     * @test
     * 
     * @return void
     */
    public function test_can_return_a_contact()
    {
        $contact = Contact::factory()->for(Company::factory())->create();

        $user = User::factory()->create();

        //$response = $this->actingAs($user, 'api')->json('GET', "/api/contacts/{$contact->id}");
        $response = $this->json('GET', "/api/contacts/{$contact->id}");

        $response->assertStatus(200)
            ->assertExactJson([
                'id'           => $contact->id,
                'company_id'   => $contact->company_id,
                'name'         => $contact->name,
                'phone_number' => $contact->phone_number,
                'created_at'   => $contact->created_at,
                'updated_at'   => $contact->updated_at,
                'notes'        => [],
            ]);
            
    }

    /**
     * @test
     * 
     * @return void
     */
    public function test_fail_with_404_when_contact_not_found()
    {
        $user = User::factory()->create();

        //$response = $this->actingAs($user, 'api')->json('GET', 'api/contacts/0');
        $response = $this->json('GET', 'api/contacts/0');

        $response->assertStatus(404);
    }

    /**
     * test
     *
     * @return void
     */
    public function test_can_return_products_collection_paginated()
    {
        list($contact1, $contact2, $contact3) = Contact::factory()->for(Company::factory())->count(3)->create();

        $user = User::factory()->create();

        //$response = $this->actingAs($user, 'api')->json('GET', '/api/contacts');
        $response = $this->json('GET', '/api/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'  => [
                    '*' => ['id', 'company_id', 'name', 'phone_number', 'created_at', 'updated_at',],
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta'  => [
                    'current_page',
                    'last_page',
                    'from',
                    'to',
                    'path',
                    'per_page',
                    'total',
                ],
            ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_fail_with_404_when_updatable_contact_not_found()
    {
        $user = User::factory()->create();

        //$response = $this->actingAs($user, 'api')->json('PUT', 'api/contacts/0');
        $response = $this->json('PUT', 'api/contacts/0');

        $response->assertStatus(404);
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_can_update_a_contact()
    {
        $contact = Contact::factory()->for(Company::factory())->create();

        $user = User::factory()->create();

        /*
        $response = $this->actingAs($user, 'api')->json('PUT', "api/contacts/{$contact->id}", [
            'name'         => $newContactName = $contact->name . ' The Second',
            'phone_number' => $contact->phone_number,
        ]);
        */
        $response = $this->json('PUT', "api/contacts/{$contact->id}", [
            'name'         => $newContactName = $contact->name . ' The Second',
            'phone_number' => $contact->phone_number,
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseHas('contacts', [
            'id'           => $contact->id,
            'company_id'   => $contact->company_id,
            'name'         => $newContactName,
            'phone_number' => $contact->phone_number,
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_fail_with_404_when_deletable_contact_not_found()
    {
        $user = User::factory()->create();

        //$response = $this->actingAs($user, 'api')->json('DELETE', 'api/contacts/0');
        $response = $this->json('DELETE', 'api/contacts/0');

        $response->assertStatus(404);
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_can_delete_a_contact()
    {
        $contact = Contact::factory()->for(Company::factory())->create();

        $user = User::factory()->create();

        //$response = $this->actingAs($user, 'api')->json('DELETE', "api/contacts/{$contact->id}");
        $response = $this->json('DELETE', "api/contacts/{$contact->id}");

        $response->assertStatus(204)
            ->assertSee(null);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }
}
