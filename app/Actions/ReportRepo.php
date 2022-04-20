<?php

namespace DailyRecipe\Actions;

use DailyRecipe\Entities\Models\Entity;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Facades\Activity;
use DailyRecipe\Facades\Activity as ActivityService;

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
    public function create(string $content, string $description, Entity $entity): Report
    {
        $userId = user()->id;
        $report = $this->report->newInstance();

        $report->content = $content;
        $report->description = $description;
        $report->user_id = $userId;
        $report->status = 1;
        $entity->reports()->save($report);
        ActivityService::addForEntity($entity, ActivityType::REPORT_RECIPE);

        return $report;
    }

    /**
     * Update the given recipe.
     */
    public function update(Report $report): Report
    {
        $report->status = 0;

        $this->report->update($report);
        Activity::addForEntity($report, ActivityType::REPORT_UPDATE);

        return $report;
    }
}