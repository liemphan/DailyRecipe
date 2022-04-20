<?php

namespace DailyRecipe\Http\Controllers;

use DailyRecipe\Actions\RequestRepo;
use DailyRecipe\Auth\User;
use DailyRecipe\Auth\UserRepo;
use Illuminate\Http\Request;

class RequestController extends Controller
{

    protected $requestRepo;
    protected $userRepo;

    public function __construct(RequestRepo $requestRepo, UserRepo $userRepo)
    {
        $this->requestRepo = $requestRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * Send request become Editor to Admin
     *
     */
    public function sendRequest(Request $request)
    {
        $this->requestRepo->create();
        $this->showSuccessNotification(trans('entities.request_editor_success'));

        return redirect(\user()->getEditUrl());
    }

    /**
     * Send request become Editor to Admin
     *
     */
    public function acceptRequest(Request $request, int $id)
    {
        $user = $this->userRepo->getById($id);
        $roles = array(2);
        $requests = $this->requestRepo->getFirstByCreateBy($id);
        $this->userRepo->setUserRoles($user, $roles);
        $this->requestRepo->updateStatus($requests, 2);
        return redirect('/settings/users');
    }

    /**
     * Send request become Editor to Admin
     *
     */
    public function rejectRequest(Request $request, int $id)
    {
        $requests = $this->requestRepo->getFirstByCreateBy($id);
        $this->requestRepo->updateStatus($requests, 3);
        return redirect('/settings/users');
    }
}