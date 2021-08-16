<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * store a contact
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $company = Company::findOrFail($request->company_id);

        $contact = $company->contacts()->create([
            'name'         => $request->name,
            'phone_number' => $request->phone_number,
        ]);


        return response()->json(new ContactResource($contact), 201);
    }

    /**
     * get a paginated collection of contacts
     *
     * @return void
     */
    public function index()
    {
        return new ContactCollection(Contact::with('notes')->paginate());
    }

    /**
     * show a contact
     *
     * @param Contact $contact
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Contact $contact)
    {
        $contact->load('notes');
        return response()->json(new ContactResource($contact));
    }

    /**
     * update a contact
     *
     * @param Request $request
     * @param Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        $contact->update([
            'name'         => $request->name,
            'phone_number' => $request->phone_number,
        ]);

        return response()->noContent();
    }

    /**
     * delete a contact
     *
     * @param Contact $contact
     * @return void
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->noContent();
    }
}
