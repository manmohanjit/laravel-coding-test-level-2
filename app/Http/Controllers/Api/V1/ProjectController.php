<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Http\Resources\Project as ProjectResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    /**
     * Create the controller instance and set the
     * authorizer for the controller
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:sanctum'])->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $projects = Project::query();

        $sortBy = $request->input('sortBy', 'name'); // default: name, possible values: created_at/updated_at/name
        $sortDirection = strtoupper($request->input('sortDirection', 'ASC')); // default: asc, possible values: asc/desc
        $pageSize = max((int) $request->input('pageSize', 3), 1); // default: 3, min 1
        $pageIndex = (int) $request->input('pageIndex', 0); // default: 0

        if(in_array($sortBy, ['created_at', 'updated_at', 'name'])) {
            $projects->orderBy(
                $sortBy,
                in_array($sortDirection, ['ASC', 'DESC']) ? $sortDirection : 'ASC'
            );
        }

        if($request->has('q')) {
            $search = $request->input('q');

            $projects->where('name', 'LIKE', "%".$search."%");
        }

        // Built-in pagination links seem to mess up when using page=0
        // as starting point. Therefor, we do the offsetting manually.
        // Ideally, we extend it so that it works and can be re-used
        // easily as well as provide consistent API responses with page
        // numbers
        $projects->skip($pageIndex * $pageSize)->take($pageSize);

        $projects = $projects->get();

        return ProjectResource::collection($projects)
            ->response();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProjectRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();

        $project = Project::create($data);

        return ProjectResource::make($project)
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Project $project)
    {
        return ProjectResource::make($project)
            ->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProjectRequest  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $data = $request->validated();

        $project->fill($data);
        $project->save();

        return ProjectResource::make($project)
            ->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Project $project)
    {
        // Only PRODUCT_OWNER role can create a project and tasks
        $this->authorize('access-project-apis');

        $project->delete();

        return response()->noContent();
    }
}
