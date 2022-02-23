<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Http\Resources\Project as ProjectResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

    protected function getFilterParams(Request $request) : array
    {
        // Sort By: default: name, possible values: created_at/updated_at/name
        $sortBy = $request->input('sortBy', 'name');
        $sortBy = in_array($sortBy, ['created_at', 'updated_at', 'name']) ? $sortBy : 'name';

        // Sort Direction: default: asc, possible values: asc/desc
        $sortDirection = strtoupper($request->input('sortDirection', 'ASC'));
        $sortDirection = in_array($sortDirection, ['ASC', 'DESC']) ? $sortDirection : 'ASC';

        // Page Size: default: 3, min 1
        $pageSize = max((int) $request->input('pageSize', 3), 1);

        // Page Index: default: 0
        $pageIndex = (int) $request->input('pageIndex', 0);

        // Search filter: default: null
        $search = $request->input('q');

        return [$sortBy, $sortDirection, $pageSize, $pageIndex, $search];
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        [$sortBy, $sortDirection, $pageSize, $pageIndex, $search] = static::getFilterParams($request);

        $cacheKey = md5(implode(',', [$sortBy, $sortDirection, $pageSize, $pageIndex, $search]));

        $cache = Cache::supportsTags() ? Cache::tags(['projects']) : Cache::getFacadeRoot();

        $projects = $cache->remember($cacheKey, 30, function() use($sortBy, $sortDirection, $pageSize, $pageIndex, $search) {
            $projects = Project::query();

            $projects->orderBy($sortBy, $sortDirection);

            // Built-in pagination links seem to mess up when using page=0
            // as starting point. Therefor, we do the offsetting manually.
            // Ideally, we extend it so that it works and can be re-used
            // easily as well as provide consistent API responses with page
            // numbers
            $projects->skip($pageIndex * $pageSize)->take($pageSize);

            if($search) {
                $projects->where('name', 'LIKE', "%".$search."%");
            }

            return $projects->get();
        });

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
