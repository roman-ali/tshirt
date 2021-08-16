<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Note;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
    use RefreshDatabase;

    /*
    /**
     * @test
     *
     * @return void
     * /
    public function test_unauthenticated_users_cannot_access_note_api_endpoints()
    {
        $index = $this->json('GET', '/api/notes');
        $index->assertStatus(401);

        $store = $this->json('POST', '/api/notes');
        $store->assertStatus(401);

        $show = $this->json('GET', '/api/notes/0');
        $show->assertStatus(401);

        $update = $this->json('PUT', '/api/notes/0');
        $update->assertStatus(401);

        $destroy = $this->json('DELETE', '/api/notes/0');
        $destroy->assertStatus(401);
    }
    */

    /**
     * @test
     * 
     * @return void
     */
    public function test_can_create_a_note()
    {
        $faker = Factory::create();

        $user = User::factory()->create();

        $contact = Contact::factory()->for(Company::factory())->create();

        /*
        $response = $this->actingAs($user, 'api')->json('POST', '/api/notes', [
            'contact_id' => $contact->id,
            'note'       => $note = $faker->sentence,
        ]);
        */
        $response = $this->json('POST', '/api/notes', [
            'contact_id' => $contact->id,
            'note'       => $note = $faker->sentence,
        ]);

        $response->assertJsonStructure([
            'id', 'contact_id', 'note', 'created_at', 'updated_at',
        ])->assertJson([
            'contact_id' => $contact->id,
            'note'       => $note,
        ])->assertStatus(201);

        $this->assertDatabaseHas('notes', [
            'contact_id' => $contact->id,
            'note'       => $note,
        ]);
    }

    /**
     * @test
     * 
     * @return void
     */
    public function test_can_return_a_note()
    {
        $note = Note::factory()->for(Contact::factory()->for(Company::factory()))->create();

        $user = User::factory()->create();

        //$response = $this->actingAs($user, 'api')->json('GET', "/api/notes/{$note->id}");
        $response = $this->json('GET', "/api/notes/{$note->id}");

        $response->assertStatus(200)
            ->assertExactJson([
                'id'         => $note->id,
                'contact_id' => $note->contact_id,
                'note'       => $note->note,
                'created_at' => $note->created_at,
                'updated_at' => $note->updated_at,
            ]);
            
    }

    /**
     * @test
     * 
     * @return void
     */
    public function test_fail_with_404_when_note_not_found()
    {
        $user = User::factory()->create();

        //$response = $this->actingAs($user, 'api')->json('GET', 'api/notes/0');
        $response = $this->json('GET', 'api/notes/0');

        $response->assertStatus(404);
    }

    /**
     * test
     *
     * @return void
     */
    public function test_can_return_products_collection_paginated()
    {
        list($note1, $note2, $note3) = Note::factory()->for(Contact::factory()->for(Company::factory()))->count(3)->create();

        $user = User::factory()->create();

        //$response = $this->actingAs($user, 'api')->json('GET', '/api/notes');
        $response = $this->json('GET', '/api/notes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'  => [
                    '*' => ['id', 'contact_id', 'note', 'created_at', 'updated_at',],
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
    public function test_fail_with_404_when_updatable_note_not_found()
    {
        $user = User::factory()->create();

        //$response = $this->actingAs($user, 'api')->json('PUT', 'api/notes/0');
        $response = $this->json('PUT', 'api/notes/0');

        $response->assertStatus(404);
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_can_update_a_note()
    {
        $note = Note::factory()->for(Contact::factory()->for(Company::factory()))->create();

        $user = User::factory()->create();

        /*
        $response = $this->actingAs($user, 'api')->json('PUT', "api/notes/{$note->id}", [
            'note' => $newNote = $note->note . ' - Amended',
        ]);
        */
        $response = $this->json('PUT', "api/notes/{$note->id}", [
            'note' => $newNote = $note->note . ' - Amended',
        ]);

        $response->assertStatus(204);

        $this->assertDatabaseHas('notes', [
            'id'         => $note->id,
            'contact_id' => $note->contact_id,
            'note'       => $newNote,
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_fail_with_404_when_deletable_note_not_found()
    {
        $user = User::factory()->create();

        //$response = $this->actingAs($user, 'api')->json('DELETE', 'api/notes/0');
        $response = $this->json('DELETE', 'api/notes/0');

        $response->assertStatus(404);
    }

    /**
     * @test
     *
     * @return void
     */
    public function test_can_delete_a_note()
    {
        $note = Note::factory()->for(Contact::factory()->for(Company::factory()))->create();

        $user = User::factory()->create();

        //$response = $this->actingAs($user, 'api')->json('DELETE', "api/notes/{$note->id}");
        $response = $this->json('DELETE', "api/notes/{$note->id}");

        $response->assertStatus(204)
            ->assertSee(null);

        $this->assertDatabaseMissing('notes', [
            'id' => $note->id,
        ]);
    }
}
