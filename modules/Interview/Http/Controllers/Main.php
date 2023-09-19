<?php

namespace Modules\Interview\Http\Controllers;

use App\Abstracts\Http\ApiController;
use Illuminate\Http\Response;
use App\Events\Auth\LandingPageShowing;
use App\Models\Auth\Role;
use App\Jobs\Document\CreateDocument;
use App\Http\Requests\Document\Document as Request;
use App\Http\Resources\Document\Document as Resource;

class Main extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->response('interview::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function createUser()
    {
        $u = new \stdClass();
        $u->landing_pages = [];

        event(new LandingPageShowing($u));

        $landing_pages = $u->landing_pages;

        $roles = Role::all()->reject(function ($r) {
            $status = $r->hasPermission('read-client-portal');

            if ($r->name == 'employee') {
                $status = true;
            }

            return $status;
        })->pluck('display_name', 'id');

        $companies = user()->companies()->take(setting('default.select_limit'))->get()->sortBy('name')->pluck('name', 'id');

        $roles_url = $this->getCloudRolesPageUrl();

        return view('auth.users.create', compact('roles', 'companies', 'landing_pages', 'roles_url'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTransaction(Request $request)
    {
        $document = $this->dispatch(new CreateDocument($request));

        return $this->created(route('api.documents.show', $document->id), new Resource($document));
    }
}
