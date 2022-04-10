<?php

namespace DailyRecipe\Actions;

use DailyRecipe\Auth\Permissions\PermissionService;
use DailyRecipe\Entities\Models\Entity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use DailyRecipe\Facades\Activity as ActivityService;
use League\CommonMark\CommonMarkConverter;
class ReportRepo
{
    protected $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }
    /**
     * Get a report by ID.
     */
    public function getById(int $id): Report
    {
        return $this->report->newQuery()->findOrFail($id);
    }
    /**
     * Create a new report on an entity.
     */
    public function create(Entity $entity, string $content, string $description): Report
    {
        $userId = user()->id;
        $report = $this->report->newInstance();

        $report->content = $content;
        $report->description = $description;
        $report->user_id = $userId;

        $entity->reports()->save($report);
        ActivityService::addForEntity($entity, ActivityType::REPORT_RECIPE);

        return $report;
    }
}