<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Books, Menus, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => 'اخیرا ایجاد شده',
    'recently_created_pages' => 'صفحات اخیرا ایجاد شده',
    'recently_updated_pages' => 'صفحاتی که اخیرا روزآمد شده‌اند',
    'recently_created_chapters' => 'فصل های اخیرا ایجاد شده',
    'recently_created_books' => 'کتاب های اخیرا ایجاد شده',
    'recently_created_menus' => 'قفسه کتاب های اخیرا ایجاد شده',
    'recently_update' => 'اخیرا به روز شده',
    'recently_viewed' => 'اخیرا مشاهده شده',
    'recent_activity' => 'فعالیت های اخیر',
    'create_now' => 'اکنون یکی ایجاد کنید',
    'revisions' => 'بازبینی‌ها',
    'meta_revision' => 'بازبینی #:revisionCount',
    'meta_created' => 'ایجاد شده :timeLength',
    'meta_created_name' => 'ایجاد شده :timeLength توسط :user',
    'meta_updated' => 'به روزرسانی شده :timeLength',
    'meta_updated_name' => 'به روزرسانی شده :timeLength توسط :user',
    'meta_owned_name' => 'توسط :user ایجاد شده‌است',
    'entity_select' => 'انتخاب موجودیت',
    'images' => 'عکس ها',
    'my_recent_drafts' => 'پیش نویس های اخیر من',
    'my_recently_viewed' => 'بازدیدهای اخیر من',
    'my_most_viewed_favourites' => 'محبوب ترین موارد مورد علاقه من',
    'my_favourites' => 'مورد علاقه من',
    'no_pages_viewed' => 'شما هیچ صفحه ای را مشاهده نکرده اید',
    'no_pages_recently_created' => 'اخیرا هیچ صفحه ای ایجاد نشده است',
    'no_pages_recently_updated' => 'اخیرا هیچ صفحه ای به روزرسانی نشده است',
    'export' => 'خروجی',
    'export_html' => 'فایل وب موجود است',
    'export_pdf' => 'فایل PDF',
    'export_text' => 'پرونده متنی ساده',
    'export_md' => 'راهنما مارک‌دون',

    // Permissions and restrictions
    'permissions' => 'مجوزها',
    'permissions_intro' => 'پس از فعال شدن، این مجوزها نسبت به مجوزهای تعیین شده نقش اولویت دارند.',
    'permissions_enable' => 'مجوزهای سفارشی را فعال کنید',
    'permissions_save' => 'ذخيره مجوزها',
    'permissions_owner' => 'مالک',

    // Search
    'search_results' => 'نتایج جستجو',
    'search_total_results_found' => ':count result found|:count total results found',
    'search_clear' => 'پاک کردن جستجو',
    'search_no_pages' => 'هیچ صفحه ای با این جستجو مطابقت ندارد',
    'search_for_term' => 'جستجو برای :term',
    'search_more' => 'نتایج بیشتر',
    'search_advanced' => 'جستجوی پیشرفته',
    'search_terms' => 'عبارات جستجو',
    'search_content_type' => 'نوع محتوا',
    'search_exact_matches' => 'مطابقت کامل',
    'search_tags' => 'جستجوها را برچسب بزنید',
    'search_options' => 'گزینه ها',
    'search_viewed_by_me' => 'بازدید شده به وسیله من',
    'search_not_viewed_by_me' => 'توسط من مشاهده نشده است',
    'search_permissions_set' => 'Permissions set',
    'search_created_by_me' => 'Created by me',
    'search_updated_by_me' => 'Updated by me',
    'search_owned_by_me' => 'Owned by me',
    'search_date_options' => 'Date Options',
    'search_updated_before' => 'Updated before',
    'search_updated_after' => 'Updated after',
    'search_created_before' => 'Created before',
    'search_created_after' => 'Created after',
    'search_set_date' => 'Set Date',
    'search_update' => 'Update Search',

    // Menus
    'menu' => 'Menu',
    'menus' => 'Menus',
    'x_menus' => ':count Menu|:count Menus',
    'menus_long' => 'Recipemenus',
    'menus_empty' => 'No menus have been created',
    'menus_create' => 'Create New Menu',
    'menus_popular' => 'Popular Menus',
    'menus_new' => 'New Menus',
    'menus_new_action' => 'New Menu',
    'menus_popular_empty' => 'The most popular menus will appear here.',
    'menus_new_empty' => 'The most recently created menus will appear here.',
    'menus_save' => 'Save Menu',
    'menus_books' => 'Books on this menu',
    'menus_add_books' => 'Add books to this menu',
    'menus_drag_books' => 'Drag books here to add them to this menu',
    'menus_empty_contents' => 'This menu has no books assigned to it',
    'menus_edit_and_assign' => 'Edit menu to assign books',
    'menus_edit_named' => 'Edit Recipemenu :name',
    'menus_edit' => 'Edit Recipemenu',
    'menus_delete' => 'Delete Recipemenu',
    'menus_delete_named' => 'Delete Recipemenu :name',
    'menus_delete_explain' => "This will delete the recipemenu with the name ':name'. Contained books will not be deleted.",
    'menus_delete_confirmation' => 'Are you sure you want to delete this recipemenu?',
    'menus_permissions' => 'Recipemenu Permissions',
    'menus_permissions_updated' => 'Recipemenu Permissions Updated',
    'menus_permissions_active' => 'Recipemenu Permissions Active',
    'menus_permissions_cascade_warning' => 'Permissions on recipemenus do not automatically cascade to contained books. This is because a book can exist on multiple menus. Permissions can however be copied down to child books using the option found below.',
    'menus_copy_permissions_to_books' => 'Copy Permissions to Books',
    'menus_copy_permissions' => 'Copy Permissions',
    'menus_copy_permissions_explain' => 'This will apply the current permission settings of this recipemenu to all books contained within. Before activating, ensure any changes to the permissions of this recipemenu have been saved.',
    'menus_copy_permission_success' => 'Recipemenu permissions copied to :count books',

    // Books
    'book' => 'Book',
    'books' => 'Books',
    'x_books' => ':count Book|:count Books',
    'books_empty' => 'No books have been created',
    'books_popular' => 'Popular Books',
    'books_recent' => 'Recent Books',
    'books_new' => 'New Books',
    'books_new_action' => 'New Book',
    'books_popular_empty' => 'The most popular books will appear here.',
    'books_new_empty' => 'The most recently created books will appear here.',
    'books_create' => 'Create New Book',
    'books_delete' => 'Delete Book',
    'books_delete_named' => 'Delete Book :bookName',
    'books_delete_explain' => 'This will delete the book with the name \':bookName\'. All pages and chapters will be removed.',
    'books_delete_confirmation' => 'Are you sure you want to delete this book?',
    'books_edit' => 'Edit Book',
    'books_edit_named' => 'Edit Book :bookName',
    'books_form_book_name' => 'Book Name',
    'books_save' => 'Save Book',
    'books_permissions' => 'Book Permissions',
    'books_permissions_updated' => 'Book Permissions Updated',
    'books_empty_contents' => 'No pages or chapters have been created for this book.',
    'books_empty_create_page' => 'Create a new page',
    'books_empty_sort_current_book' => 'Sort the current book',
    'books_empty_add_chapter' => 'Add a chapter',
    'books_permissions_active' => 'Book Permissions Active',
    'books_search_this' => 'Search this book',
    'books_navigation' => 'Book Navigation',
    'books_sort' => 'Sort Book Contents',
    'books_sort_named' => 'Sort Book :bookName',
    'books_sort_name' => 'Sort by Name',
    'books_sort_created' => 'Sort by Created Date',
    'books_sort_updated' => 'Sort by Updated Date',
    'books_sort_chapters_first' => 'Chapters First',
    'books_sort_chapters_last' => 'Chapters Last',
    'books_sort_show_other' => 'Show Other Books',
    'books_sort_save' => 'Save New Order',

    // Chapters
    'chapter' => 'Chapter',
    'chapters' => 'Chapters',
    'x_chapters' => ':count Chapter|:count Chapters',
    'chapters_popular' => 'Popular Chapters',
    'chapters_new' => 'New Chapter',
    'chapters_create' => 'Create New Chapter',
    'chapters_delete' => 'Delete Chapter',
    'chapters_delete_named' => 'Delete Chapter :chapterName',
    'chapters_delete_explain' => 'This will delete the chapter with the name \':chapterName\'. All pages that exist within this chapter will also be deleted.',
    'chapters_delete_confirm' => 'Are you sure you want to delete this chapter?',
    'chapters_edit' => 'Edit Chapter',
    'chapters_edit_named' => 'Edit Chapter :chapterName',
    'chapters_save' => 'Save Chapter',
    'chapters_move' => 'Move Chapter',
    'chapters_move_named' => 'Move Chapter :chapterName',
    'chapter_move_success' => 'Chapter moved to :bookName',
    'chapters_permissions' => 'Chapter Permissions',
    'chapters_empty' => 'No pages are currently in this chapter.',
    'chapters_permissions_active' => 'Chapter Permissions Active',
    'chapters_permissions_success' => 'Chapter Permissions Updated',
    'chapters_search_this' => 'Search this chapter',

    // Pages
    'page' => 'Page',
    'pages' => 'Pages',
    'x_pages' => ':count Page|:count Pages',
    'pages_popular' => 'Popular Pages',
    'pages_new' => 'New Page',
    'pages_attachments' => 'Attachments',
    'pages_navigation' => 'Page Navigation',
    'pages_delete' => 'Delete Page',
    'pages_delete_named' => 'Delete Page :pageName',
    'pages_delete_draft_named' => 'Delete Draft Page :pageName',
    'pages_delete_draft' => 'Delete Draft Page',
    'pages_delete_success' => 'Page deleted',
    'pages_delete_draft_success' => 'Draft page deleted',
    'pages_delete_confirm' => 'Are you sure you want to delete this page?',
    'pages_delete_draft_confirm' => 'Are you sure you want to delete this draft page?',
    'pages_editing_named' => 'Editing Page :pageName',
    'pages_edit_draft_options' => 'Draft Options',
    'pages_edit_save_draft' => 'Save Draft',
    'pages_edit_draft' => 'Edit Page Draft',
    'pages_editing_draft' => 'Editing Draft',
    'pages_editing_page' => 'Editing Page',
    'pages_edit_draft_save_at' => 'Draft saved at ',
    'pages_edit_delete_draft' => 'Delete Draft',
    'pages_edit_discard_draft' => 'Discard Draft',
    'pages_edit_set_changelog' => 'Set Changelog',
    'pages_edit_enter_changelog_desc' => 'Enter a brief description of the changes you\'ve made',
    'pages_edit_enter_changelog' => 'Enter Changelog',
    'pages_save' => 'Save Page',
    'pages_title' => 'Page Title',
    'pages_name' => 'Page Name',
    'pages_md_editor' => 'Editor',
    'pages_md_preview' => 'Preview',
    'pages_md_insert_image' => 'Insert Image',
    'pages_md_insert_link' => 'Insert Entity Link',
    'pages_md_insert_drawing' => 'Insert Drawing',
    'pages_not_in_chapter' => 'Page is not in a chapter',
    'pages_move' => 'Move Page',
    'pages_move_success' => 'Page moved to ":parentName"',
    'pages_copy' => 'Copy Page',
    'pages_copy_desination' => 'Copy Destination',
    'pages_copy_success' => 'Page successfully copied',
    'pages_permissions' => 'Page Permissions',
    'pages_permissions_success' => 'Page permissions updated',
    'pages_revision' => 'Revision',
    'pages_revisions' => 'Page Revisions',
    'pages_revisions_named' => 'Page Revisions for :pageName',
    'pages_revision_named' => 'Page Revision for :pageName',
    'pages_revision_restored_from' => 'Restored from #:id; :summary',
    'pages_revisions_created_by' => 'Created By',
    'pages_revisions_date' => 'Revision Date',
    'pages_revisions_number' => '#',
    'pages_revisions_numbered' => 'Revision #:id',
    'pages_revisions_numbered_changes' => 'Revision #:id Changes',
    'pages_revisions_changelog' => 'Changelog',
    'pages_revisions_changes' => 'Changes',
    'pages_revisions_current' => 'Current Version',
    'pages_revisions_preview' => 'Preview',
    'pages_revisions_restore' => 'Restore',
    'pages_revisions_none' => 'This page has no revisions',
    'pages_copy_link' => 'Copy Link',
    'pages_edit_content_link' => 'Edit Content',
    'pages_permissions_active' => 'Page Permissions Active',
    'pages_initial_revision' => 'Initial publish',
    'pages_initial_name' => 'New Page',
    'pages_editing_draft_notification' => 'You are currently editing a draft that was last saved :timeDiff.',
    'pages_draft_edited_notification' => 'This page has been updated by since that time. It is recommended that you discard this draft.',
    'pages_draft_page_changed_since_creation' => 'This page has been updated since this draft was created. It is recommended that you discard this draft or take care not to overwrite any page changes.',
    'pages_draft_edit_active' => [
        'start_a' => ':count users have started editing this page',
        'start_b' => ':userName has started editing this page',
        'time_a' => 'since the page was last updated',
        'time_b' => 'in the last :minCount minutes',
        'message' => ':start :time. Take care not to overwrite each other\'s updates!',
    ],
    'pages_draft_discarded' => 'Draft discarded, The editor has been updated with the current page content',
    'pages_specific' => 'Specific Page',
    'pages_is_template' => 'Page Template',

    // Editor Sidebar
    'page_tags' => 'Page Tags',
    'chapter_tags' => 'Chapter Tags',
    'book_tags' => 'Book Tags',
    'menu_tags' => 'Menu Tags',
    'tag' => 'Tag',
    'tags' =>  'Tags',
    'tag_name' =>  'Tag Name',
    'tag_value' => 'Tag Value (Optional)',
    'tags_explain' => "Add some tags to better categorise your content. \n You can assign a value to a tag for more in-depth organisation.",
    'tags_add' => 'Add another tag',
    'tags_remove' => 'Remove this tag',
    'tags_usages' => 'Total tag usages',
    'tags_assigned_pages' => 'Assigned to Pages',
    'tags_assigned_chapters' => 'Assigned to Chapters',
    'tags_assigned_books' => 'Assigned to Books',
    'tags_assigned_menus' => 'Assigned to Menus',
    'tags_x_unique_values' => ':count unique values',
    'tags_all_values' => 'All values',
    'tags_view_tags' => 'View Tags',
    'tags_view_existing_tags' => 'View existing tags',
    'tags_list_empty_hint' => 'Tags can be assigned via the page editor sidebar or while editing the details of a book, chapter or menu.',
    'attachments' => 'Attachments',
    'attachments_explain' => 'Upload some files or attach some links to display on your page. These are visible in the page sidebar.',
    'attachments_explain_instant_save' => 'Changes here are saved instantly.',
    'attachments_items' => 'Attached Items',
    'attachments_upload' => 'Upload File',
    'attachments_link' => 'Attach Link',
    'attachments_set_link' => 'Set Link',
    'attachments_delete' => 'Are you sure you want to delete this attachment?',
    'attachments_dropzone' => 'Drop files or click here to attach a file',
    'attachments_no_files' => 'No files have been uploaded',
    'attachments_explain_link' => 'You can attach a link if you\'d prefer not to upload a file. This can be a link to another page or a link to a file in the cloud.',
    'attachments_link_name' => 'Link Name',
    'attachment_link' => 'Attachment link',
    'attachments_link_url' => 'Link to file',
    'attachments_link_url_hint' => 'Url of site or file',
    'attach' => 'Attach',
    'attachments_insert_link' => 'Add Attachment Link to Page',
    'attachments_edit_file' => 'Edit File',
    'attachments_edit_file_name' => 'File Name',
    'attachments_edit_drop_upload' => 'Drop files or click here to upload and overwrite',
    'attachments_order_updated' => 'Attachment order updated',
    'attachments_updated_success' => 'Attachment details updated',
    'attachments_deleted' => 'Attachment deleted',
    'attachments_file_uploaded' => 'File successfully uploaded',
    'attachments_file_updated' => 'File successfully updated',
    'attachments_link_attached' => 'Link successfully attached to page',
    'templates' => 'Templates',
    'templates_set_as_template' => 'Page is a template',
    'templates_explain_set_as_template' => 'You can set this page as a template so its contents be utilized when creating other pages. Other users will be able to use this template if they have view permissions for this page.',
    'templates_replace_content' => 'Replace page content',
    'templates_append_content' => 'Append to page content',
    'templates_prepend_content' => 'Prepend to page content',

    // Profile View
    'profile_user_for_x' => 'User for :time',
    'profile_created_content' => 'Created Content',
    'profile_not_created_pages' => ':userName has not created any pages',
    'profile_not_created_chapters' => ':userName has not created any chapters',
    'profile_not_created_books' => ':userName has not created any books',
    'profile_not_created_menus' => ':userName has not created any menus',

    // Comments
    'comment' => 'Comment',
    'comments' => 'Comments',
    'comment_add' => 'Add Comment',
    'comment_placeholder' => 'Leave a comment here',
    'comment_count' => '{0} No Comments|{1} 1 Comment|[2,*] :count Comments',
    'comment_save' => 'Save Comment',
    'comment_saving' => 'Saving comment...',
    'comment_deleting' => 'Deleting comment...',
    'comment_new' => 'New Comment',
    'comment_created' => 'commented :createDiff',
    'comment_updated' => 'Updated :updateDiff by :username',
    'comment_deleted_success' => 'Comment deleted',
    'comment_created_success' => 'Comment added',
    'comment_updated_success' => 'Comment updated',
    'comment_delete_confirm' => 'Are you sure you want to delete this comment?',
    'comment_in_reply_to' => 'In reply to :commentId',

    // Revision
    'revision_delete_confirm' => 'Are you sure you want to delete this revision?',
    'revision_restore_confirm' => 'Are you sure you want to restore this revision? The current page contents will be replaced.',
    'revision_delete_success' => 'Revision deleted',
    'revision_cannot_delete_latest' => 'Cannot delete the latest revision.',
];
