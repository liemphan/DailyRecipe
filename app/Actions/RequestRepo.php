<?php

namespace DailyRecipe\Actions;

use DailyRecipe\Entities\Models\Entity;
use DailyRecipe\Entities\Models\Requests;
use DailyRecipe\Facades\Activity as ActivityService;
use Illuminate\Database\Eloquent\Collection;

class RequestRepo
{

    protected $request;

    public function __construct(Requests $request)
    {
        $this->request = $request;
    }

    /**
     * Get a report by ID.
     */
    public function getById(int $id): Requests
    {
        return $this->request->newQuery()->findOrFail($id);
    }

    /**
     * Create a new report on an entity.
     */
    public function create(): Requests
    {
        $userId = user()->id;
        $request = $this->request->newInstance();

        $request->status = 1;
        $request->created_by = $userId;
        $request->save();
//        ActivityService::addForEntity($entity, ActivityType::REQUEST_EDITOR);

        return $request;
    }


    /**
     * Get a report by ID.
     */
    public function getFirstByCreateBy(int $userId): ?Requests
    {
        return $this->request->newQuery()->where('created_by', $userId)->first();
    }

    /**
     * Get a report by ID.
     */
    public function getAllRequests(): Collection
    {
//        return $this->request->newQuery()->findOrFail($userId,'created_by');
        return $this->request->all();
    }


    /**
     * Update an existing report status.
     */
    public function updateStatus(Requests $requests, int $status): Requests
    {
        $requests->user_id = user()->id;
        $requests->status = $status;
        $requests->save();

        return $requests;
    }
}