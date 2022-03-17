{!! csrf_field() !!}

<div class="card content-wrap">
    <h1 class="list-heading">{{ $title }}</h1>

    <div class="setting-list">

        <div class="grid half">
            <div>
                <label class="setting-list-label">{{ trans('settings.role_details') }}</label>
            </div>
            <div>
                <div class="form-group">
                    <label for="display_name">{{ trans('settings.role_name') }}</label>
                    @include('form.text', ['name' => 'display_name'])
                </div>
                <div class="form-group">
                    <label for="description">{{ trans('settings.role_desc') }}</label>
                    @include('form.text', ['name' => 'description'])
                </div>
                <div class="form-group">
                    @include('form.checkbox', ['name' => 'mfa_enforced', 'label' => trans('settings.role_mfa_enforced') ])
                </div>

                @if(in_array(config('auth.method'), ['ldap', 'saml2', 'oidc']))
                    <div class="form-group">
                        <label for="name">{{ trans('settings.role_external_auth_id') }}</label>
                        @include('form.text', ['name' => 'external_auth_id'])
                    </div>
                @endif
            </div>
        </div>

        <div permissions-table>
            <label class="setting-list-label">{{ trans('settings.role_system') }}</label>
            <a href="#" permissions-table-toggle-all class="text-small text-primary">{{ trans('common.toggle_all') }}</a>

            <div class="toggle-switch-list grid half mt-m">
                <div>
                    <div>@include('settings.roles.parts.checkbox', ['permission' => 'restrictions-manage-all', 'label' => trans('settings.role_manage_entity_permissions')])</div>
                    <div>@include('settings.roles.parts.checkbox', ['permission' => 'restrictions-manage-own', 'label' => trans('settings.role_manage_own_entity_permissions')])</div>
                    <div>@include('settings.roles.parts.checkbox', ['permission' => 'templates-manage', 'label' => trans('settings.role_manage_page_templates')])</div>
                    <div>@include('settings.roles.parts.checkbox', ['permission' => 'access-api', 'label' => trans('settings.role_access_api')])</div>
                    <div>@include('settings.roles.parts.checkbox', ['permission' => 'content-export', 'label' => trans('settings.role_export_content')])</div>
                </div>
                <div>
                    <div>@include('settings.roles.parts.checkbox', ['permission' => 'settings-manage', 'label' => trans('settings.role_manage_settings')])</div>
                    <div>@include('settings.roles.parts.checkbox', ['permission' => 'users-manage', 'label' => trans('settings.role_manage_users')])</div>
                    <div>@include('settings.roles.parts.checkbox', ['permission' => 'user-roles-manage', 'label' => trans('settings.role_manage_roles')])</div>
                    <p class="text-warn text-small mt-s mb-none">{{ trans('settings.roles_system_warning') }}</p>
                </div>
            </div>
        </div>

        <div>
            <label class="setting-list-label">{{ trans('settings.role_asset') }}</label>
            <p>{{ trans('settings.role_asset_desc') }}</p>

            @if (isset($role) && $role->system_name === 'admin')
                <p class="text-warn">{{ trans('settings.role_asset_admins') }}</p>
            @endif

            <table permissions-table class="table toggle-switch-list compact permissions-table">
                <tr>
                    <th width="20%">
                        <a href="#" permissions-table-toggle-all class="text-small text-primary">{{ trans('common.toggle_all') }}</a>
                    </th>
                    <th width="20%" permissions-table-toggle-all-in-column>{{ trans('common.create') }}</th>
                    <th width="20%" permissions-table-toggle-all-in-column>{{ trans('common.view') }}</th>
                    <th width="20%" permissions-table-toggle-all-in-column>{{ trans('common.edit') }}</th>
                    <th width="20%" permissions-table-toggle-all-in-column>{{ trans('common.delete') }}</th>
                </tr>
                <tr>
                    <td>
                        <div>{{ trans('entities.menus_long') }}</div>
                        <a href="#" permissions-table-toggle-all-in-row class="text-small text-primary">{{ trans('common.toggle_all') }}</a>
                    </td>
                    <td>
                        @include('settings.roles.parts.checkbox', ['permission' => 'recipemenu-create-all', 'label' => trans('settings.role_all')])
                    </td>
                    <td>
                        @include('settings.roles.parts.checkbox', ['permission' => 'recipemenu-view-own', 'label' => trans('settings.role_own')])
                        <br>
                        @include('settings.roles.parts.checkbox', ['permission' => 'recipemenu-view-all', 'label' => trans('settings.role_all')])
                    </td>
                    <td>
                        @include('settings.roles.parts.checkbox', ['permission' => 'recipemenu-update-own', 'label' => trans('settings.role_own')])
                        <br>
                        @include('settings.roles.parts.checkbox', ['permission' => 'recipemenu-update-all', 'label' => trans('settings.role_all')])
                    </td>
                    <td>
                        @include('settings.roles.parts.checkbox', ['permission' => 'recipemenu-delete-own', 'label' => trans('settings.role_own')])
                        <br>
                        @include('settings.roles.parts.checkbox', ['permission' => 'recipemenu-delete-all', 'label' => trans('settings.role_all')])
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>{{ trans('entities.recipes') }}</div>
                        <a href="#" permissions-table-toggle-all-in-row class="text-small text-primary">{{ trans('common.toggle_all') }}</a>
                    </td>
                    <td>
                        @include('settings.roles.parts.checkbox', ['permission' => 'recipe-create-all', 'label' => trans('settings.role_all')])
                    </td>
                    <td>
                        @include('settings.roles.parts.checkbox', ['permission' => 'recipe-view-own', 'label' => trans('settings.role_own')])
                        <br>
                        @include('settings.roles.parts.checkbox', ['permission' => 'recipe-view-all', 'label' => trans('settings.role_all')])
                    </td>
                    <td>
                        @include('settings.roles.parts.checkbox', ['permission' => 'recipe-update-own', 'label' => trans('settings.role_own')])
                        <br>
                        @include('settings.roles.parts.checkbox', ['permission' => 'recipe-update-all', 'label' => trans('settings.role_all')])
                    </td>
                    <td>
                        @include('settings.roles.parts.checkbox', ['permission' => 'recipe-delete-own', 'label' => trans('settings.role_own')])
                        <br>
                        @include('settings.roles.parts.checkbox', ['permission' => 'recipe-delete-all', 'label' => trans('settings.role_all')])
                    </td>
                </tr>
{{--                <tr>--}}
{{--                    <td>--}}
{{--                        <div>{{ trans('entities.chapters') }}</div>--}}
{{--                        <a href="#" permissions-table-toggle-all-in-row class="text-small text-primary">{{ trans('common.toggle_all') }}</a>--}}
{{--                    </td>--}}
{{--                    <td>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'chapter-create-own', 'label' => trans('settings.role_own')])--}}
{{--                        <br>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'chapter-create-all', 'label' => trans('settings.role_all')])--}}
{{--                    </td>--}}
{{--                    <td>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'chapter-view-own', 'label' => trans('settings.role_own')])--}}
{{--                        <br>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'chapter-view-all', 'label' => trans('settings.role_all')])--}}
{{--                    </td>--}}
{{--                    <td>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'chapter-update-own', 'label' => trans('settings.role_own')])--}}
{{--                        <br>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'chapter-update-all', 'label' => trans('settings.role_all')])--}}
{{--                    </td>--}}
{{--                    <td>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'chapter-delete-own', 'label' => trans('settings.role_own')])--}}
{{--                        <br>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'chapter-delete-all', 'label' => trans('settings.role_all')])--}}
{{--                    </td>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <td>--}}
{{--                        <div>{{ trans('entities.pages') }}</div>--}}
{{--                        <a href="#" permissions-table-toggle-all-in-row class="text-small text-primary">{{ trans('common.toggle_all') }}</a>--}}
{{--                    </td>--}}
{{--                    <td>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'page-create-own', 'label' => trans('settings.role_own')])--}}
{{--                        <br>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'page-create-all', 'label' => trans('settings.role_all')])--}}
{{--                    </td>--}}
{{--                    <td>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'page-view-own', 'label' => trans('settings.role_own')])--}}
{{--                        <br>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'page-view-all', 'label' => trans('settings.role_all')])--}}
{{--                    </td>--}}
{{--                    <td>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'page-update-own', 'label' => trans('settings.role_own')])--}}
{{--                        <br>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'page-update-all', 'label' => trans('settings.role_all')])--}}
{{--                    </td>--}}
{{--                    <td>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'page-delete-own', 'label' => trans('settings.role_own')])--}}
{{--                        <br>--}}
{{--                        @include('settings.roles.parts.checkbox', ['permission' => 'page-delete-all', 'label' => trans('settings.role_all')])--}}
{{--                    </td>--}}
{{--                </tr>--}}
                <tr>
                    <td>
                        <div>{{ trans('entities.images') }}</div>
                        <a href="#" permissions-table-toggle-all-in-row class="text-small text-primary">{{ trans('common.toggle_all') }}</a>
                    </td>
                    <td>@include('settings.roles.parts.checkbox', ['permission' => 'image-create-all', 'label' => ''])</td>
                    <td style="line-height:1.2;"><small class="faded">{{ trans('settings.role_controlled_by_asset') }}</small></td>
                    <td>
                        @include('settings.roles.parts.checkbox', ['permission' => 'image-update-own', 'label' => trans('settings.role_own')])
                        <br>
                        @include('settings.roles.parts.checkbox', ['permission' => 'image-update-all', 'label' => trans('settings.role_all')])
                    </td>
                    <td>
                        @include('settings.roles.parts.checkbox', ['permission' => 'image-delete-own', 'label' => trans('settings.role_own')])
                        <br>
                        @include('settings.roles.parts.checkbox', ['permission' => 'image-delete-all', 'label' => trans('settings.role_all')])
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>{{ trans('entities.attachments') }}</div>
                        <a href="#" permissions-table-toggle-all-in-row class="text-small text-primary">{{ trans('common.toggle_all') }}</a>
                    </td>
                    <td>@include('settings.roles.parts.checkbox', ['permission' => 'attachment-create-all', 'label' => ''])</td>
                    <td style="line-height:1.2;"><small class="faded">{{ trans('settings.role_controlled_by_asset') }}</small></td>
                    <td>
                        @include('settings.roles.parts.checkbox', ['permission' => 'attachment-update-own', 'label' => trans('settings.role_own')])
                        <br>
                        @include('settings.roles.parts.checkbox', ['permission' => 'attachment-update-all', 'label' => trans('settings.role_all')])
                    </td>
                    <td>
                        @include('settings.roles.parts.checkbox', ['permission' => 'attachment-delete-own', 'label' => trans('settings.role_own')])
                        <br>
                        @include('settings.roles.parts.checkbox', ['permission' => 'attachment-delete-all', 'label' => trans('settings.role_all')])
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>{{ trans('entities.comments') }}</div>
                        <a href="#" permissions-table-toggle-all-in-row class="text-small text-primary">{{ trans('common.toggle_all') }}</a>
                    </td>
                    <td>@include('settings.roles.parts.checkbox', ['permission' => 'comment-create-all', 'label' => ''])</td>
                    <td style="line-height:1.2;"><small class="faded">{{ trans('settings.role_controlled_by_asset') }}</small></td>
                    <td>
                        @include('settings.roles.parts.checkbox', ['permission' => 'comment-update-own', 'label' => trans('settings.role_own')])
                        <br>
                        @include('settings.roles.parts.checkbox', ['permission' => 'comment-update-all', 'label' => trans('settings.role_all')])
                    </td>
                    <td>
                        @include('settings.roles.parts.checkbox', ['permission' => 'comment-delete-own', 'label' => trans('settings.role_own')])
                        <br>
                        @include('settings.roles.parts.checkbox', ['permission' => 'comment-delete-all', 'label' => trans('settings.role_all')])
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="form-group text-right">
        <a href="{{ url("/settings/roles") }}" class="button outline">{{ trans('common.cancel') }}</a>
        @if (isset($role) && $role->id)
            <a href="{{ url("/settings/roles/delete/{$role->id}") }}" class="button outline">{{ trans('settings.role_delete') }}</a>
        @endif
        <button type="submit" class="button">{{ trans('settings.role_save') }}</button>
    </div>

</div>

<div class="card content-wrap auto-height">
    <h2 class="list-heading">{{ trans('settings.role_users') }}</h2>
    @if(count($role->users ?? []) > 0)
        <div class="grid third">
            @foreach($role->users as $user)
                <div class="user-list-item">
                    <div>
                        <img class="avatar small" src="{{ $user->getAvatar(40) }}" alt="{{ $user->name }}">
                    </div>
                    <div>
                        @if(userCan('users-manage') || user()->id == $user->id)
                            <a href="{{ url("/settings/users/{$user->id}") }}">
                                @endif
                                {{ $user->name }}
                                @if(userCan('users-manage') || user()->id == $user->id)
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">
            {{ trans('settings.role_users_none') }}
        </p>
    @endif
</div>
