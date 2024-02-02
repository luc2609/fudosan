<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CloseProjectRequest;
use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\CreateReportProjectRequest;
use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\EditReportProjectRequest;
use App\Http\Requests\GetListProjectRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Services\ProjectService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(GetListProjectRequest $request)
    {
        try {
            return $this->projectService->index($request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateProjectRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateProjectRequest $request)
    {
        DB::beginTransaction();
        try {
            $params = $request->all();
            $user = auth()->user();
            $checkParamsProject = $this->projectService->checkParamsProject($user, $params, null);
            if ($checkParamsProject) {
                return $checkParamsProject;
            }
            $data = $this->projectService->create($params);
            DB::commit();
            return _success($data, __('message.created_success'), HTTP_SUCCESS);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            return $this->projectService->showProject($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function showReportProject($id)
    {
        try {
            return $this->projectService->showReportProject($id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProjectRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProjectRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $params = $request->all();
            $user = auth()->user();
            $userId = $user->id;
            $checkProject = $this->projectService->checkProject($id);
            $checkRoles = $this->projectService->checkRoleUpdate($id, $userId);
            $checkParamsProject = $this->projectService->checkParamsProject($user, $params, $id);
            if ($checkProject) {
                $response = $checkProject;
            } else if ($checkRoles) {
                $response = $checkRoles;
            } else if ($checkParamsProject) {
                $response = $checkParamsProject;
            } else {
                $data = $this->projectService->update($params, $id);
                DB::commit();
                $response = _success($data, __('message.updated_success'), HTTP_SUCCESS);
            }

            return $response;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $userId = auth()->user()->id;
            $checkProject = $this->projectService->checkProject($id);
            $checkRoles = $this->projectService->checkRoles($id, $userId);
            if ($checkProject) {
                $response = $checkProject;
            } else if ($checkRoles) {
                $response = $checkRoles;
            } else {
                $this->projectService->delete($id);
                DB::commit();
                $response = _success(null, __('message.deleted_success'), HTTP_SUCCESS);
            }
            return  $response;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * @param CreateReportProjectRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function createReport(CreateReportProjectRequest $request, $id)
    {
        try {
            return $this->projectService->createReportProject($request, $id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * @param EditReportProjectRequest $request
     * @param $id
     * @param $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateReport(EditReportProjectRequest $request, $id, $postId)
    {
        try {
            return $this->projectService->updateReportProject($request, $id, $postId);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * @param CreateCommentRequest $request
     * @param $id
     * @param $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function createComment(CreateCommentRequest $request, $id, $postId)
    {
        try {
            return $this->projectService->createComment($request, $id, $postId);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * @param UpdateCommentRequest $request
     * @param $id
     * @param $postId
     * @param $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateComment(UpdateCommentRequest $request, $id, $postId, $commentId)
    {
        try {
            return $this->projectService->updateComment($request, $id, $postId, $commentId);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * @param $id
     * @param $postId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePost($id, $postId)
    {
        DB::beginTransaction();
        try {
            $post = $this->projectService->deletePost($id, $postId);
            DB::commit();
            return $post;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * @param $id
     * @param $postId
     * @param $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteComment($id, $postId, $commentId)
    {
        try {
            return $this->projectService->deleteComment($id, $postId, $commentId);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * @param CloseProjectRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateClose(CloseProjectRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $params = $request->all();
            $user = auth()->user();
            $checkProject = $this->projectService->checkProject($id);
            $checkRoleClose = $this->projectService->checkRoleClose($id, $params);
            if ($checkProject) {
                $response = $checkProject;
            } else if ($checkRoleClose) {
                $response = $checkRoleClose;
            } else {
                $data = $this->projectService->updateClose($id, $params, $user);
                DB::commit();
                $response = _success($data, __('message.updated_success'), HTTP_SUCCESS);
            }
            return $response;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showHistory($id)
    {
        try {
            return $this->projectService->showProjectHistory($id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    /**
     * @param GetListProjectRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexRequestClose(GetListProjectRequest $request)
    {
        try {
            return $this->projectService->indexRequestClose($request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function updatePhase(Request $request, $projectId)
    {
        try {
            return $this->projectService->updatePhase($projectId, $request);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }

    public function cancelProject($id)
    {
        try {
            return $this->projectService->cancelProject($id);
        } catch (Exception $e) {
            Log::error(__METHOD__ . ' - ' . __LINE__ . ' : ' . $e->getMessage());
            return _errorSystem();
        }
    }
}
