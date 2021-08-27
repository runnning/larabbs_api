<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $permissions=$request->user()->getAllPermissions();
        PermissionResource::wrap('data');
        return PermissionResource::collection($permissions);
    }
}
