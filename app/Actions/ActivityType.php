<?php

namespace DailyRecipe\Actions;

class ActivityType
{
    const PAGE_CREATE = 'page_create';
    const PAGE_UPDATE = 'page_update';
    const PAGE_DELETE = 'page_delete';
    const PAGE_RESTORE = 'page_restore';
    const PAGE_MOVE = 'page_move';

    const CHAPTER_CREATE = 'chapter_create';
    const CHAPTER_UPDATE = 'chapter_update';
    const CHAPTER_DELETE = 'chapter_delete';
    const CHAPTER_MOVE = 'chapter_move';

    const RECIPE_CREATE = 'recipe_create';
    const RECIPE_UPDATE = 'recipe_update';
    const RECIPE_DELETE = 'recipe_delete';
    const RECIPE_SORT = 'recipe_sort';

    const RECIPEMENU_CREATE = 'menu_create';
    const RECIPEMENU_UPDATE = 'menu_update';
    const RECIPEMENU_DELETE = 'menu_delete';

    const COMMENTED_ON = 'commented_on';
    const PERMISSIONS_UPDATE = 'permissions_update';

    const SETTINGS_UPDATE = 'settings_update';
    const MAINTENANCE_ACTION_RUN = 'maintenance_action_run';

    const RECYCLE_BIN_EMPTY = 'recycle_bin_empty';
    const RECYCLE_BIN_RESTORE = 'recycle_bin_restore';
    const RECYCLE_BIN_DESTROY = 'recycle_bin_destroy';

    const USER_CREATE = 'user_create';
    const USER_UPDATE = 'user_update';
    const USER_DELETE = 'user_delete';

    const API_TOKEN_CREATE = 'api_token_create';
    const API_TOKEN_UPDATE = 'api_token_update';
    const API_TOKEN_DELETE = 'api_token_delete';

    const ROLE_CREATE = 'role_create';
    const ROLE_UPDATE = 'role_update';
    const ROLE_DELETE = 'role_delete';

    const AUTH_PASSWORD_RESET = 'auth_password_reset_request';
    const AUTH_PASSWORD_RESET_UPDATE = 'auth_password_reset_update';
    const AUTH_LOGIN = 'auth_login';
    const AUTH_REGISTER = 'auth_register';

    const MFA_SETUP_METHOD = 'mfa_setup_method';
    const MFA_REMOVE_METHOD = 'mfa_remove_method';
}
