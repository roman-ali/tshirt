<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Company;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @return void
     */
    /*
    public function test_unauthenticated_users_cannot_access_company_api_endpoints()
    {
        $index = $this->json('GET', '/api/companies');
        $index->assertStatus(401);
        
        $store = $this->json('POST', '/api/companies');
        $store->assertStatus(401);
        
        $show = $this->json('GET', '/api/companies/0');
        $show->assertStatus(401);
        
        $update = $this->json('PUT', '/api/companies/0');
        $update->assertStatus(401);
        
        $destroy = $this->json('DELETE', '/api/companies/0');
        $destroy->assertStatus(401);
    }
    */

    /**
     * @test
     * 
     * @return void
     */
    public function test_can_create_a_company()
    {
        $faker = Factory::create();

        $user = User::factory()->create();
     
        /*
        $response = $this->actingAs($user, 'api')->json('POST', '/api/companies', [
            'name' => $name = $faker->company,
            'slug' => Str::slug($name),
        ]);
        */
        $response = $this->json('POST', '/api/companies', [
            'name' => $name = $faker->company,
            'slug' => Str::slug($name),
        ]);

        $response->assertJsonStructure([
            'id', 'name', 'slug', 'created_at', 'updated_at',
        ])->assertJson([
            'name' => $name,
            'slug' => Str::slug($name),
        ])->assertStatus(201);

        $this->assertDatabaseHas('companies', [
            'name' => $name,
            'slug' => Str::slug($name),
        ]);
    }

    /**
     * @test
     * 
     * @return void
     */
    public function test_can_return_a_company()
    {
        $company = Company::factory()->create();

        $user = User::factory()->create();
     
        //$response = $this->actingAs($user, 'api')->json('GET', "/api/companies/{$company->id}");
        $response = $this->actingAs($user, 'api')->json('GET', "/api/companies/{$company->id}");

        $response->assertStatus(200)
            ->assertExactJson([
                'id'         => $company->id,
                'name'       => $company->name,
                'slug'       => $company->slug,
                'created_at' => $company->created_at,
                'updated_at' => $company->updated_at,
                'contacts'   => [],
            ]);
    }

    /**
     * @test
     * 
     * @return void
     */
    public function test_fail_with_404_when_company_not_found()
    {
        $user = User::factory()->create();
     
        //$response = $this->actingAs($user, 'api')->json('GET', 'api/companies/0');
        $response = $this->json('GET', 'api/companies/0');

        $response->assertStatus(404);
    }

    /**
     * test
     *
     * @return void
     */
    public function test_can_return_products_collection_paginated()
    {
        list($company1, $company2, $company3) = Company::factory()->count(3)->create();
     
        $user = User::factory()->create();
     
        //$response = $this->actingAs($user, 'api')->json('GET', '/api/companies');
        $response = $this->json('GET', '/api/companies');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'  => [
                    '*' => ['id', 'name', 'slug', 'created_at', 'updated_at',],
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
    public function test_fail_with_404_when_updatable_company_not_found()
    {
        $user = User::factory()->create();
     
        //$response = $this->actingAs($user, 'api')->json('PUT', 'api/companies/0');
        $response = $this->json('PUT', 'api/companies/0');

        $response->assertStatus(404);
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_can_update_a_company()
    {
        $company = Company::factory()->create();

        $user = User::factory()->create();
     
        /*
        $response = $this->actingAs($user, 'api')->json('PUT', "api/companies/{$company->id}", [
            'name' => $newCompanyName = $company->name . '_updated',
        ]);
        */
        $response = $this->json('PUT', "api/companies/{$company->id}", [
            'name' => $newCompanyName = $company->name . '_updated',
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseHas('companies', [
            'id'   => $company->id,
            'name' => $newCompanyName,
            'slug' => Str::slug($newCompanyName),
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_fail_with_404_when_deletable_company_not_found()
    {
        $user = User::factory()->create();
     
        //$response = $this->actingAs($user, 'api')->json('DELETE', 'api/companies/0');
        $response = $this->json('DELETE', 'api/companies/0');

        $response->assertStatus(404);
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_can_delete_a_company()
    {
        $company = Company::factory()->create();

        $user = User::factory()->create();
     
        //$response = $this->actingAs($user, 'api')->json('DELETE', "api/companies/{$company->id}");
        $response = $this->json('DELETE', "api/companies/{$company->id}");

        $response->assertStatus(204)
            ->assertSee(null);

        $this->assertDatabaseMissing('companies', [
            'id' => $company->id,
        ]);
    }
}
