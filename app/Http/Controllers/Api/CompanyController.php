<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyCollection;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    /**
     * store a company
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $company = Company::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json(new CompanyResource($company), 201);
    }

    /**
     * get a paginated collection of companies
     *
     * @return void
     */
    public function index()
    {
        return new CompanyCollection(Company::with('contacts')->paginate());
    }

    /**
     * show a company
     *
     * @param Company $company
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Company $company)
    {
        $company->load('contacts');
        return response()->json(new CompanyResource($company));
    }

    /**
     * update a company
     *
     * @param Request $request
     * @param Company $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $company->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->noContent();
    }

    /**
     * delete a company
     *
     * @param Company $company
     * @return void
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return response()->noContent();
    }
}
