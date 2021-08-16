<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NoteCollection;
use App\Http\Resources\NoteResource;
use App\Models\Contact;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * store a note
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $contact = Contact::findOrFail($request->contact_id);
        
        $note = $contact->notes()->create([
            'note' => $request->note,
        ]);

        return response()->json(new NoteResource($note), 201);
    }

    /**
     * get a paginated collection of notes
     *
     * @return void
     */
    public function index()
    {
        return new NoteCollection(Note::paginate());
    }

    /**
     * get a paginated collection of notes by contact
     *
     * @param Contact $contact
     * @return \Illuminate\Http\JsonResponse
     */
    public function showByContact(Contact $contact)
    {
        return new NoteCollection($contact->notes()->paginate());
    }

    /**
     * show a note
     *
     * @param Note $note
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Note $note)
    {
        return response()->json(new NoteResource($note));
    }

    /**
     * update a note
     *
     * @param Request $request
     * @param Note $note
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Note $note)
    {
        $note->update([
            'note' => $request->note,
        ]);

        return response()->noContent();
    }

    /**
     * delete a note
     *
     * @param Note $note
     * @return void
     */
    public function destroy(Note $note)
    {
        $note->delete();

        return response()->noContent();
    }
}
