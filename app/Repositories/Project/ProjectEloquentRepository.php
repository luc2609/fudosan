<?php

namespace App\Repositories\Project;

use App\Models\Calendar;
use App\Models\Comment;
use App\Models\Customer;
use App\Models\MasterPhaseProject;
use App\Models\ProjectUser;
use App\Models\Post;
use App\Models\Project;
use App\Models\ProjectCustomer;
use App\Models\ProjectPhase;
use App\Models\ProjectProperty;
use App\Models\Property;
use App\Repositories\Base\BaseEloquentRepository;
use Illuminate\Support\Facades\DB;

class ProjectEloquentRepository extends BaseEloquentRepository implements ProjectRepositoryInterface
{
    public function getModel()
    {
        return Project::class;
    }

    public function getList()
    {
        return $this->_model;
    }

    public function show($id)
    {
        return $this->_model
            ->leftJoin('divisions', 'divisions.id', 'projects.division_id')
            ->leftJoin('project_phases', 'project_phases.id', 'projects.current_phase_id')
            ->leftJoin('master_phase_projects', 'master_phase_projects.id', 'project_phases.m_phase_project_id')
            ->select([
                'projects.*',
                'divisions.name as division_name',
                'project_phases.m_phase_project_id',
                'master_phase_projects.name as master_phase_project_name'
            ])
            ->with(['customers', 'projectFiles', 'propertyAndStations', 'salePurposes', 'purchasePurposes', 'advertisingWebs', 'properties', 'calendars'])
            ->where('projects.id', $id)->first();
    }

    public function showProject($id)
    {
        $url = env('AWS_URL') . '/';
        return Post::leftJoin('users', 'users.id', 'posts.created_id')
            ->join('user_roles', 'user_roles.user_id', 'posts.created_id')
            ->join('projects', 'projects.id', 'posts.project_id')
            ->leftJoin(DB::raw('(select post_id, MAX(updated_at) as max_update_at from comments group by  post_id ) as
            comments_date'), 'comments_date.post_id', 'posts.id')
            ->select(
                'posts.id',
                'projects.division_id',
                'posts.created_id as user_id',
                'posts.title',
                'user_roles.role_id',
                'posts.updated_at',
                DB::raw('CONCAT(last_name, " ", first_name) as username'),
                DB::raw('CONCAT("' . $url . '", users.avatar) as avatar'),
                'comments_date.max_update_at'
            )
            ->with('comments', function ($q) use ($url) {
                return $q->leftJoin('users', 'users.id', 'comments.created_id')
                    ->leftJoin('user_roles', 'user_roles.user_id', 'users.id')
                    ->select(
                        'comments.id',
                        'comments.created_id as user_id',
                        'comments.post_id',
                        'comments.content',
                        'comments.created_at',
                        'comments.updated_at',
                        'user_roles.role_id',
                        DB::raw('CONCAT(last_name, " ", first_name) as username'),
                        DB::raw('CONCAT("' . $url . '", users.avatar) as avatar'),
                    );
            })
            ->where('posts.project_id', $id)
            ->orderBy(DB::raw('CASE WHEN max_update_at is not null THEN  max_update_at ELSE posts.created_at
            END'), 'asc')
            ->get();
    }

    public function index($request, $companyId, $user)
    {
        if ($user->hasRole(MANAGER_ROLE)) {
            $userDivisions = $user->divisions;
            $divisionIds = [];
            foreach ($userDivisions as $userDivision) {
                array_push($divisionIds, $userDivision['id']);
            }
        }
        $query = $this->_model
            ->leftJoin('divisions', 'divisions.id', 'projects.division_id')
            ->leftJoin('project_phases', 'project_phases.id', 'projects.current_phase_id')
            ->leftJoin('master_phase_projects', 'master_phase_projects.id', 'project_phases.m_phase_project_id')
            ->select(
                'projects.id',
                'projects.title',
                'projects.type',
                'projects.division_id',
                'divisions.name as division_name',
                'project_phases.m_phase_project_id',
                'master_phase_projects.name as master_phase_project_name',
                'projects.revenue',
                'projects.close_status',
            )
            ->with('customers', 'properties')
            ->where('projects.company_id', $companyId);
        if ($request->action == PROJECT_ME) {
            $query->leftJoin('project_users', 'project_users.project_id', 'projects.id')
                ->where('project_users.user_id', $user['id']);
        }
        if ($request->action == PROJECT_ALL) {
            $query;
        }
        if ($request->action == PROJECT_DIVISION) {
            if ($user->hasRole(MANAGER_ROLE)) {
                $query->whereIn('projects.division_id', $divisionIds);
            }
            if ($user->hasRole(USER_ROLE)) {
                $query->where('division_id', $user['division']);
            }
        }
        if ($request->division_id) {
            $query->where('projects.division_id', $request->division_id);
        }
        if ($request->type) {
            $query->where('projects.type', $request->type);
        }
        if ($request->keyword) {
            $query->where('projects.title', 'like BINARY', '%' . $request->keyword . '%');
        }
        if ($request->phase_id) {
            $query->where('project_phases.m_phase_project_id', $request->phase_id);
        }
        if ($request->status) {
            if ($request->status == REQUEST_CLOSE) {
                $query->where('projects.close_status', IN_PROGRESS);
            } else {
                $query->where('projects.close_status', $request->status);
            }
        }
        return $query->orderBy('projects.id', 'DESC');
    }

    public function createReportProject($request, $id, $userId)
    {
        $params = [
            'title' => $request->title,
            'project_id' => $id,
            'created_id' => $userId
        ];
        return Post::create($params);
    }

    public function updatReportProject($request, $postId)
    {
        $post = Post::find($postId);
        $post->update(['title' => $request->title]);
        return $post;
    }

    public function createComment($request, $postId, $userId)
    {
        return   Comment::create([
            'content' => $request->content,
            'post_id' => $postId,
            'created_id' => $userId
        ]);
    }

    public function delete($id)
    {
        $project = $this->_model->find($id);

        $project->users()->detach();
        $project->documents()->delete();
        $project->advertisingWebs()->detach();
        $project->delete();
    }

    public function updateComment($request, $comment_id)
    {
        $comment = Comment::find($comment_id);
        $comment->update(['content' => $request->content]);
        return $comment;
    }

    public function deletePost($id)
    {
        $post = Post::find($id);
        if ($post) {
            $post->comments()->delete();
            $post->delete();
            return true;
        }
        return false;
    }

    public function deleteComment($postId, $commentId)
    {
        $comment = Comment::where('id', $commentId)->where('post_id', $postId)->first();
        if ($comment) {
            $comment->delete();
            return true;
        }
        return false;
    }

    //count project of Staff
    public function countProjectUser($id)
    {
        $paramClose = [FAIL_CLOSE, SUCCESS_CLOSE];
        $paramInProgress = [IN_PROGRESS, REQUEST_CLOSE, REJECT_CLOSE];
        $projectUsers = ProjectUser::where('user_id', $id)->count();
        $projectInProgress = ProjectUser::where('user_id', $id)
            ->leftJoin('projects', 'projects.id', 'project_users.project_id')
            ->whereIn('projects.close_status', $paramInProgress)->count();
        $projectClose = ProjectUser::where('user_id', $id)
            ->leftJoin('projects', 'projects.id', 'project_users.project_id')
            ->whereIn('projects.close_status', $paramClose)->count();
        $quantityProjects = [
            'total_project_count' => $projectUsers,
            'in_progress_project_count' => $projectInProgress,
            'closed_project_count' => $projectClose,
        ];
        return $quantityProjects;
    }

    public function getProjectUser($request, $id)
    {
        $paramClose = [FAIL_CLOSE, SUCCESS_CLOSE];
        $paramInProgress = [IN_PROGRESS, REQUEST_CLOSE, REJECT_CLOSE];
        $query = Project::leftJoin('project_users', 'project_users.project_id', 'projects.id')
            ->leftJoin('divisions', 'divisions.id', 'projects.division_id')
            ->leftJoin('project_phases', 'project_phases.id', 'projects.current_phase_id')
            ->leftJoin('master_phase_projects', 'master_phase_projects.id', 'project_phases.m_phase_project_id')
            ->select(
                'projects.id',
                'projects.title',
                'projects.type',
                'divisions.name as division_name',
                'master_phase_projects.name as master_phase_project_name',
                'master_phase_projects.id as master_phase_project_id',
                'projects.price',
                'projects.close_status',
                'projects.revenue'
            )
            ->with('customers')
            ->where('project_users.user_id', $id)
            ->where('project_users.deleted_at', null);
        if ($request->project_type == PROJECT_IN_PROGRESS) {
            $query->whereIn('projects.close_status', $paramInProgress);
        }
        if ($request->project_type == PROJECT_CLOSE) {
            $query->whereIn('projects.close_status', $paramClose);
        }
        if ($request->title) {
            $query->where('projects.title', 'like BINARY', '%' . $request->title . '%');
        }
        if ($request->project_phase_id) {
            $query->where('master_phase_projects.id', $request->project_phase_id);
        }
        return $query->orderBy('projects.id', 'DESC');
    }

    // Project division
    public function countProjectDivision($id)
    {
        $companyId = auth()->user()->company;
        $paramClose = [FAIL_CLOSE, SUCCESS_CLOSE];
        $paramInProgress = [IN_PROGRESS, REQUEST_CLOSE, REJECT_CLOSE];
        $projectDivisions = $this->_model->where('projects.company_id', $companyId)
            ->where('division_id', $id)->count();

        $projectInProgress = $this->_model->where('projects.company_id', $companyId)
            ->where('division_id', $id)
            ->whereIn('projects.close_status', $paramInProgress)->count();

        $projectClose = $this->_model->where('projects.company_id', $companyId)
            ->where('division_id', $id)
            ->whereIn('projects.close_status', $paramClose)->count();
        return [
            'total_project_count' => $projectDivisions,
            'in_progress_project_count' => $projectInProgress,
            'closed_project_count' => $projectClose,
        ];
    }

    public function listProjectDivision($request, $id)
    {
        $companyId = auth()->user()->company;
        $paramClose = [FAIL_CLOSE, SUCCESS_CLOSE];
        $paramInProgress = [IN_PROGRESS, REQUEST_CLOSE, REJECT_CLOSE];
        $query = $this->_model->where('projects.company_id', $companyId)
            ->where('division_id', $id)
            ->leftJoin('divisions', 'divisions.id', 'projects.division_id')
            ->leftJoin('project_phases', 'project_phases.id', 'projects.current_phase_id')
            ->leftJoin('master_phase_projects', 'master_phase_projects.id', 'project_phases.m_phase_project_id')
            ->select(
                'projects.id',
                'projects.title',
                'projects.close_status',
                'master_phase_projects.name as master_phase_project_name',
                'master_phase_projects.id as master_phase_project_id',
                'projects.price',
                'projects.revenue'

            )
            ->with('customers');
        if ($request->project_type == PROJECT_IN_PROGRESS) {
            $query->whereIn('projects.close_status', $paramInProgress)->count();
        }
        if ($request->project_type == PROJECT_CLOSE) {
            $query->whereIn('projects.close_status', $paramClose)->count();
        }
        if ($request->title) {
            $query->where('projects.title', 'like BINARY', '%' . $request->title . '%');
        }
        if ($request->project_phase_id) {
            $query->where('master_phase_projects.id', $request->project_phase_id);
        }
        return $query->orderBy('projects.id', 'DESC');
    }

    public function showProjectHistory($id)
    {
        $project = $this->_model->find($id);
        return [
            'history' => $project->history,
            'title' => $project->title
        ];
    }

    public function rankingTotal($companyId, $params, $typeRanking)
    {
        if ($typeRanking == TOTAL_RANKING_USER) {
            $query = $this->_model
                ->select('projects.id', 'projects.price', 'projects.revenue')
                ->leftJoin('divisions', 'divisions.id', 'projects.division_id')
                ->where('projects.company_id', $companyId)
                ->where('projects.close_status', SUCCESS_CLOSE)
                ->with(['projectUsers' => function ($q) {
                    $q->select('project_users.project_id', 'users.commission_rate')
                        ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
                        ->leftJoin('users', 'project_users.user_id', 'users.id');
                }]);
        } else if ($typeRanking == TOTAL_RANKING_DIVISION) {
            $query = $this->_model
                ->select(DB::raw("COUNT(*) AS total_project, SUM(price) AS total_revenue, SUM(revenue) AS total_brokerage_fee"))
                ->leftJoin('divisions', 'divisions.id', 'projects.division_id')
                ->where('projects.company_id', $companyId)
                ->where('projects.close_status', SUCCESS_CLOSE);
        }


        if (isset($params['start_date']) && isset($params['end_date'])) {
            $query->whereBetween('projects.updated_at', [$params['start_date'], $params['end_date']]);
        }
        if (!empty($params['divisions'])) {
            $query->whereIn('division_id', $params['divisions']);
        }

        return $query->get();
    }

    // brokerage fee
    public function brokerageFeeOfProject($propertyIds)
    {
        $sum = 0;
        foreach ($propertyIds as $propertyId) {
            $property = Property::find($propertyId);
            if ($property->price < CONDITION_REVENUE_MIN) {
                $brokerageFee = (($property->price * 5) / 100) + ((($property->price * 5) / 100) * 10) / 100;
            } else if ($property->price < CONDITION_REVENUE_MAX) {
                $brokerageFee = (($property->price * 4) / 100 + 20000) + ((($property->price * 4) / 100 + 20000) * 10) / 100;
            } else {
                $brokerageFee = (($property->price * 3) / 100 + 60000) + ((($property->price * 3) / 100 + 60000) * 10) / 100;
            }
            $sum += $brokerageFee;
        }
        return $sum;
    }

    // price project ( revenue project)
    public function revenueOfProject($propertyIds)
    {
        $sum = 0;
        foreach ($propertyIds as $propertyId) {
            $property = Property::find($propertyId);
            $price = $property->price;
            $sum += $price;
        }
        return $sum;
    }


    public function createTitleProject($projectId, $projectPhaseId)
    {
        $project = $this->_model->find($projectId);
        $projectPhase = ProjectPhase::find($projectPhaseId);
        $phaseName = MasterPhaseProject::find($projectPhase->m_phase_project_id)->name;
        $customerIds = ProjectCustomer::where('project_id', $projectId)->first();
        $customer =  Customer::find($customerIds['customer_id']);
        if ($project) {
            $project->title = '【' . $phaseName . '】' . ' ' . $customer->last_name . ' ' . $customer->first_name . 'さま ';
            $project->save();
            return true;
        }
        return false;
    }

    //count project of customer
    public function countProjectCustomer($id)
    {
        $paramClose = [FAIL_CLOSE, SUCCESS_CLOSE];
        $paramInProgress = [IN_PROGRESS, REQUEST_CLOSE, REJECT_CLOSE];
        $projectCustomers = ProjectCustomer::where('customer_id', $id)->count();
        $projectInProgress = ProjectCustomer::where('customer_id', $id)
            ->leftJoin('projects', 'projects.id', 'project_customers.project_id')
            ->whereIn('projects.close_status', $paramInProgress)->count();
        $projectClose = ProjectCustomer::where('customer_id', $id)
            ->leftJoin('projects', 'projects.id', 'project_customers.project_id')
            ->whereIn('projects.close_status', $paramClose)->count();
        $quantityProjects = [
            'total_project_count' => $projectCustomers,
            'in_progress_project_count' => $projectInProgress,
            'closed_project_count' => $projectClose,
        ];
        return $quantityProjects;
    }

    // List Project Customer
    public function listProjectCustomer($request, $id)
    {
        $paramClose = [FAIL_CLOSE, SUCCESS_CLOSE];
        $paramInProgress = [IN_PROGRESS, REQUEST_CLOSE, REJECT_CLOSE];
        $query = Project::leftJoin('project_customers', 'project_customers.project_id', 'projects.id')
            ->leftJoin('divisions', 'divisions.id', 'projects.division_id')
            ->leftJoin('project_phases', 'project_phases.id', 'projects.current_phase_id')
            ->leftJoin('master_phase_projects', 'master_phase_projects.id', 'project_phases.m_phase_project_id')
            ->select(
                'projects.id',
                'projects.title',
                'projects.type',
                'divisions.name as division_name',
                'master_phase_projects.name as master_phase_project_name',
                'master_phase_projects.id as master_phase_project_id',
                'projects.price',
                'projects.close_status',
                'projects.revenue'
            )
            ->with('customers')
            ->where('project_customers.customer_id', $id)
            ->where('project_customers.deleted_at', null);
        if ($request->project_type == PROJECT_IN_PROGRESS) {
            $query->whereIn('projects.close_status', $paramInProgress);
        }
        if ($request->project_type == PROJECT_CLOSE) {
            $query->whereIn('projects.close_status', $paramClose);
        }
        if ($request->title) {
            $query->where('projects.title', 'like BINARY', '%' . $request->title . '%');
        }
        if ($request->project_phase_id) {
            $query->where('master_phase_projects.id', $request->project_phase_id);
        }
        return $query->orderBy('projects.id', 'DESC');
    }

    public function indexRequestClose($user, $companyId, $request)
    {
        if ($user->hasRole(MANAGER_ROLE)) {
            $userDivisions = $user->divisions;
            $divisionIds = [];
            foreach ($userDivisions as $userDivision) {
                array_push($divisionIds, $userDivision['id']);
            }
        }
        $query = $this->_model
            ->leftJoin('divisions', 'divisions.id', 'projects.division_id')
            ->leftJoin('properties', 'properties.id', 'projects.property_id')
            ->leftJoin('project_phases', 'project_phases.id', 'projects.current_phase_id')
            ->leftJoin('master_phase_projects', 'master_phase_projects.id', 'project_phases.m_phase_project_id')
            ->leftJoin('project_users', 'project_users.project_id', 'projects.id')
            ->leftJoin('users', 'users.id', 'project_users.user_id')
            ->select(
                'projects.id',
                'projects.title',
                'projects.type',
                'projects.division_id',
                'properties.name as property_name',
                'divisions.name as division_name',
                'project_phases.m_phase_project_id',
                'master_phase_projects.name as master_phase_project_name',
                'properties.price as property_price'
            )
            ->with('customers')
            ->with('users')
            ->where('projects.company_id', $companyId)
            ->where('projects.close_status', REQUEST_CLOSE)
            ->where('projects.deleted_at', null)
            ->distinct();

        if ($user->hasRole(MANAGER_ROLE)) {
            $query->where(function ($q) use ($divisionIds, $user) {
                $q->whereIn('projects.division_id', $divisionIds)
                    ->orWhere('project_users.user_id', $user['id']);
            });
        }
        if ($user->hasRole(USER_ROLE)) {
            $query->where('project_users.user_id', $user['id']);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->user_id) {
            $query->where('project_users.user_id', $request->user_id);
        }

        if ($request->title) {
            $query->where('projects.title',  'like BINARY', '%' . $request->title . '%');
        }

        if ($request->division_id) {
            $query->where('projects.division_id',  $request->division_id);
        }
        return $query->orderBy('projects.id', 'DESC');
    }

    // Project property
    public function countProjectProperty($id)
    {
        $paramClose = [FAIL_CLOSE, SUCCESS_CLOSE];
        $paramInProgress = [IN_PROGRESS, REQUEST_CLOSE, REJECT_CLOSE];
        $projectProperties = ProjectProperty::where('property_id', $id)->count();
        $projectInProgress = ProjectProperty::where('project_properties.property_id', $id)
            ->leftJoin('projects', 'projects.id', 'project_properties.project_id')
            ->whereIn('projects.close_status', $paramInProgress)->count();
        $projectClose = ProjectProperty::where('project_properties.property_id', $id)
            ->leftJoin('projects', 'projects.id', 'project_properties.project_id')
            ->whereIn('projects.close_status', $paramClose)->count();
        return [
            'total_project_count' => $projectProperties,
            'in_progress_project_count' => $projectInProgress,
            'closed_project_count' => $projectClose,
        ];
    }

    public function listProjectProperty($request, $id)
    {
        $paramClose = [FAIL_CLOSE, SUCCESS_CLOSE];
        $paramInProgress = [IN_PROGRESS, REQUEST_CLOSE, REJECT_CLOSE];
        $query = Project::leftJoin('project_properties', 'project_properties.project_id', 'projects.id')
            ->leftJoin('divisions', 'divisions.id', 'projects.division_id')
            ->leftJoin('project_phases', 'project_phases.id', 'projects.current_phase_id')
            ->leftJoin('master_phase_projects', 'master_phase_projects.id', 'project_phases.m_phase_project_id')
            ->select(
                'projects.id',
                'projects.title',
                'projects.type',
                'divisions.name as division_name',
                'master_phase_projects.name as master_phase_project_name',
                'master_phase_projects.id as master_phase_project_id',
                'projects.price',
                'projects.close_status',
                'projects.revenue'
            )
            ->with('customers')
            ->where('project_properties.property_id', $id)
            ->where('project_properties.deleted_at', null);
        if ($request->project_type == PROJECT_IN_PROGRESS) {
            $query->whereIn('projects.close_status', $paramInProgress)->count();
        }
        if ($request->project_type == PROJECT_CLOSE) {
            $query->whereIn('projects.close_status', $paramClose)->count();
        }
        if ($request->title) {
            $query->where('projects.title', 'like BINARY', '%' . $request->title . '%');
        }
        if ($request->project_phase_id) {
            $query->where('master_phase_projects.id', $request->project_phase_id);
        }
        return $query->orderBy('projects.id', 'DESC');
    }

    // List project calendar
    public function listProjectCalendar($startDay)
    {
        return Calendar::leftJoin('projects', 'calendars.project_id', 'projects.id')
            ->whereIn('projects.close_status', [IN_PROGRESS, REQUEST_CLOSE, REJECT_CLOSE])
            ->where('projects.is_action_noti', IS_ACTION_NOTI)
            ->where('calendars.project_phase_id', '<>', PHASE_NINE)
            ->whereDate('calendars.meeting_end_time', $startDay)
            ->select('projects.id', 'calendars.project_phase_id', 'calendars.meeting_start_time', 'calendars.meeting_end_time', 'calendars.id as calendar_id')
            ->get();
    }

    // List project calendar
    public function listProjectCalendarCrontab($startDay)
    {
        return Calendar::leftJoin('projects', 'calendars.project_id', 'projects.id')
            ->join('project_phases', 'project_phases.id', 'projects.current_phase_id')
            ->join('project_users', 'project_users.project_id', 'projects.id')
            ->whereIn('projects.close_status', [IN_PROGRESS, REQUEST_CLOSE, REJECT_CLOSE])
            ->where('projects.is_action_noti', IS_ACTION_NOTI)
            ->where('project_users.user_type', USER_IN_CHARGE_TYPE)
            ->where('calendars.project_phase_id', '<>', PHASE_NINE)
            ->where(DB::raw("DATE_FORMAT(calendars.meeting_end_time, '%Y-%m-%d %H:%i')"), '=', $startDay)
            ->select(
                'projects.id',
                'projects.title',
                'projects.current_phase_id',
                'calendars.project_phase_id',
                'calendars.meeting_start_time',
                'calendars.meeting_end_time',
                'calendars.start_date',
                'calendars.end_date',
                'calendars.id as calendar_id',
                'project_phases.m_phase_project_id',
                'project_users.user_id'
            )
            ->get();
    }

    // Find calendar
    public function findListCalendar($projectId, $meetingEndTime, $calendarId)
    {
        return Calendar::leftJoin('projects', 'calendars.project_id', 'projects.id')
            ->select('calendars.project_id', 'calendars.start_date', 'calendars.end_date', 'calendars.project_phase_id', 'calendars.meeting_start_time', 'calendars.meeting_end_time', 'projects.current_phase_id')
            ->where('calendars.project_id', $projectId)
            ->where('calendars.meeting_end_time', '>', $meetingEndTime)
            ->where('calendars.id', '<>', $calendarId)
            ->orderBy('calendars.meeting_end_time', 'ASC')
            ->get();
    }

    public function countRequestClose($companyId, $divisionIds)
    {
        if (!$divisionIds) {
            return $this->_model
                ->where('projects.close_status', REQUEST_CLOSE)
                ->where('projects.company_id', $companyId)
                ->count();
        } else {
            return $this->_model
                ->whereIn('projects.division_id', $divisionIds)
                ->where('projects.close_status', REQUEST_CLOSE)
                ->where('projects.company_id', $companyId)
                ->count();
        }
    }

    public function dataRankingUser($project)
    {
        $userInChargeId = $project->projectUserInCharge()->user_id;
        $commissionRate = $project->users()->where('users.id', $userInChargeId)->first()->commission_rate;
        $brokerageFee = $project->revenue * $commissionRate / COMMISSION_RATE_MAX;
        $price = $project->price * $commissionRate / COMMISSION_RATE_MAX;
        return [
            'user_id' => $userInChargeId,
            'brokerage_fee_ranking' => $brokerageFee,
            'revenue_ranking' => $price
        ];
    }

    public function dataRankingDivision($projectId)
    {
        $arr = $this->_model->where('projects.id', $projectId)
            ->select('division_id', 'price as revenue_ranking', 'revenue as brokerage_fee_ranking')
            ->first()->toArray();
        unset($arr['user_in_charge_name']);
        return $arr;
    }

    public function nextBackProject($request, $id, $companyId, $user)
    {
        $projects = $this->index($request, $companyId, $user)->pluck('id');
        $arrayProject = $projects->toArray();
        $nextProject = null;
        $backProject = null;
        if (in_array($id, $arrayProject)) {
            $projectIndex =  $projects->search($id);
            if ($projectIndex == 0) {
                $backProject = null;
            } else {
                $key =  $projectIndex - POSITION;
                $backProject = $arrayProject[$key];
            }

            if ((array_key_last($arrayProject)) == $projectIndex) {
                $nextProject = null;
            } else {
                $key =  $projectIndex + POSITION;
                $nextProject = $arrayProject[$key];
            }
        }
        return [
            'next_id' => $nextProject,
            'back_id' => $backProject
        ];
    }

    public function getProjectInCompany($companyId)
    {
        return $this->_model->where('company_id', $companyId)->get();
    }
}
