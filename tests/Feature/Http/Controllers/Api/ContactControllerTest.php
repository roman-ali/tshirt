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

use function PHPUnit\Framework\assertGreaterThan;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * 
     * @return void
     */
    public function test_can_create_a_contact()
    {
        $faker = Factory::create();

        $company = Company::factory()->create();

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
        $response = $this->json('GET', 'api/contacts/0');

        $response->assertStatus(404);
    }

    /**
     * test
     *
     * @return void
     */
    public function test_can_return_paginated_collection_of_contacts()
    {
        list($contact1, $contact2, $contact3) = Contact::factory()->for(Company::factory())->count(3)->create();

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
     * test
     *
     * @return void
     */
    public function test_can_return_paginated_collection_of_contacts_by_company()
    {
        $company = Company::factory()->create();

        list($contact1, $contact2, $contact3) = Contact::factory()->for($company)->count(3)->create();

        $response = $this->json('GET', "/api/contacts/company/{$company->id}");

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
     * test
     *
     * @return void
     */
    public function test_can_return_non_empty_paginated_collection_of_contacts_by_valid_name_search()
    {

        list($contact1, $contact2, $contact3) = Contact::factory()->for(Company::factory())->count(3)->create();

        $response = $this->json('GET', "/api/contacts?name={$contact2->name}");

        $response->assertStatus(200);

        assertGreaterThan(0, count($response->decodeResponseJson()['data']));
    }

    /**
     * test
     *
     * @return void
     */
    public function test_can_return_empty_paginated_collection_of_contacts_by_invalid_name_search()
    {

        list($contact1, $contact2, $contact3) = Contact::factory()->for(Company::factory())->count(3)->create();

        $response = $this->json('GET', "/api/contacts?name=123");

        $response->assertStatus(200)->assertJsonCount(0, 'data');
    }

    /**
     * test
     *
     * @return void
     */
    public function test_can_return_non_empty_paginated_collection_of_contacts_by_valid_company_name_search()
    {

        $company = Company::factory()->create();
        list($contact1, $contact2, $contact3) = Contact::factory()->for($company)->count(3)->create();

        $response = $this->json('GET', "/api/contacts?company_name={$company->name}");

        $response->assertStatus(200);

        assertGreaterThan(0, count($response->decodeResponseJson()['data']));
    }

    /**
     * test
     *
     * @return void
     */
    public function test_can_return_empty_paginated_collection_of_contacts_by_invalid_company_name_search()
    {
        $company = Company::factory()->create();
        list($contact1, $contact2, $contact3) = Contact::factory()->for($company)->count(3)->create();

        $response = $this->json('GET', "/api/contacts?company_name=123");

        $response->assertStatus(200)->assertJsonCount(0, 'data');
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_fail_with_404_when_updatable_contact_not_found()
    {
        $response = $this->json('PATCH', 'api/contacts/0');

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

        $response = $this->json('PATCH', "api/contacts/{$contact->id}", [
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

        $response = $this->json('DELETE', "api/contacts/{$contact->id}");

        $response->assertStatus(204)
            ->assertSee(null);

        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }
}
