<?php

namespace App\Repositories\Project;

use App\Repositories\Base\BaseRepositoryInterface;

interface ProjectRepositoryInterface extends BaseRepositoryInterface
{
    public function show($id);

    public function showProject($id);

    public function index($request, $companyId, $user);

    public function createReportProject($request, $id, $userId);

    public function updatReportProject($request, $postId);

    public function createComment($request, $postId, $userId);

    public function delete($id);

    public function updateComment($request, $comment_id);

    public function deletePost($id);

    public function deleteComment($postId, $commentId);

    //count project of Staff
    public function countProjectUser($id);

    public function getProjectUser($request, $id);

    // Project division
    public function countProjectDivision($id);

    public function listProjectDivision($request, $id);

    public function showProjectHistory($id);

    public function rankingTotal($companyId, $params, $typeRanking);

    // brokerage fee
    public function brokerageFeeOfProject($propertyIds);

    // price project ( revenue project)
    public function revenueOfProject($propertyIds);

    public function createTitleProject($projectId, $projectPhaseId);

    //count project of customer
    public function countProjectCustomer($id);

    // List Project Customer
    public function listProjectCustomer($request, $id);

    public function indexRequestClose($user, $companyId, $request);

    // Project property
    public function countProjectProperty($id);

    public function listProjectProperty($request, $id);

    // List project calendar
    public function listProjectCalendar($startDay);

    // Find calendar
    public function findListCalendar($projectId, $meetingEndTime, $calendarId);

    public function countRequestClose($companyId, $divisionIds);

    public function dataRankingUser($project);

    public function dataRankingDivision($projectId);

    public function nextBackProject($request, $id, $companyId, $user);

    public function getProjectInCompany($companyId);

    public function listProjectCalendarCrontab($startDay);
}
