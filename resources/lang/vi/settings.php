<?php
/**
 * Settings text strings
 * Contains all text strings used in the general settings sections of DailyRecipe
 * including users and roles.
 */
return [

    // Common Messages
    'settings' => 'Cài đặt',
    'settings_save' => 'Lưu Cài đặt',
    'settings_save_success' => 'Đã lưu cài đặt',

    // App Settings
    'app_customization' => 'Tuỳ biến',
    'app_features_security' => 'Chức năng & Bảo mật',
    'app_name' => 'Tên Ứng dụng',
    'app_name_desc' => 'Tên này được hiển thị trong header và trong bất kì email hệ thống được gửi.',
    'app_name_header' => 'Hiển thị tên trong header',
    'app_public_access' => 'Quyền truy cập công khai',
    'app_public_access_desc' => 'Bật tùy chọn này sẽ cho phép khách, người không cần đăng nhập, truy cập đến nội dung bản DailyRecipe của bạn.',
    'app_public_access_desc_guest' => 'Quyền truy cập của khách có thể được điều khiển thông qua người dùng "Guest".',
    'app_public_access_toggle' => 'Cho phép truy cập công khai',
    'app_public_viewing' => 'Cho phép xem công khai?',
    'app_secure_images' => 'Bảo mật tốt hơn cho việc tải lên ảnh',
    'app_secure_images_toggle' => 'Bật bảo mật tốt hơn cho các ảnh được tải lên',
    'app_secure_images_desc' => 'Vì lí do hiệu năng, tất cả các ảnh đều được truy cập công khai. Tùy chọn này thêm một chuỗi ngẫu nhiên, khó đoán vào phần liên kết đến ảnh. Đảm bảo rằng tránh việc index thư mục để ngăn chặn việc truy cập đến ảnh một cách dễ dàng.',
    'app_editor' => 'Soạn thảo Trang',
    'app_editor_desc' => 'Chọn trình soạn thảo nào sẽ được sử dụng bởi tất cả người dùng để chỉnh sửa trang.',
    'app_custom_html' => 'Tùy chọn nội dung Head HTML',
    'app_custom_html_desc' => 'Bất cứ nội dung nào được thêm vào đây sẽ được đưa vào phần cuối của khu vực <head> của mỗi trang. Tiện cho việc ghi đè style hoặc thêm mã phân tích dữ liệu.',
    'app_custom_html_disabled_notice' => 'Nội dung tùy biến HTML head bị tắt tại trang cài đặt này để đảm bảo mọi thay đổi làm hỏng hệ thống có để được khôi phục.',
    'app_logo' => 'Logo Ứng dụng',
    'app_logo_desc' => 'Ảnh này nên có kích thước chiều cao là 43px. <br>Ảnh lớn sẽ được điều chỉnh tỷ lệ xuống.',
    'app_primary_color' => 'Màu chủ đạo của Ứng dụng',
    'app_primary_color_desc' => 'Đặt màu chủ đạo của ứng dụng kể cả banner, các nút và các đường dẫn liên kết.',
    'app_homepage' => 'Trang chủ Ứng dụng',
    'app_homepage_desc' => 'Chọn hiển thị để hiện tại trang chủ thay cho hiển thị mặc định. Quyền cho trang được bỏ qua cho các trang được chọn.',
    'app_homepage_select' => 'Chọn một trang',
    'app_footer_links' => 'Footer Links',
    'app_footer_links_desc' => 'Thêm liên kết để hiển thị bên trong chân trang của trang web. Chúng sẽ được hiển thị ở cuối hầu hết các trang, kể cả những trang không yêu cầu đăng nhập. Bạn có thể sử dụng nhãn của "trans::<key>" để sử dụng các bản dịch do hệ thống xác định. Ví dụ: Using "trans::common.privacy_policy" sẽ cung cấp văn bản đã dịch "Privacy Policy" and "trans::common.terms_of_service"sẽ cung cấp văn bản đã dịch "Terms of Service".',
    'app_footer_links_label' => 'Link Label',
    'app_footer_links_url' => 'Link URL',
    'app_footer_links_add' => 'Thêm Footer Link',
    'app_disable_comments' => 'Tắt bình luận',
    'app_disable_comments_toggle' => 'Tắt bình luận',
    'app_disable_comments_desc' => 'Tắt các bình luận trên tất cả các trang của ứng dụng. <br> Các bình luận đã tồn tại sẽ không được hiển thị.',

    // Color settings
    'content_colors' => 'Màu của phần Nội dung',
    'content_colors_desc' => 'Đặt màu cho tất cả các thành phần trong trang theo sự tổ chức kế thừa. Việc chọn màu sắc với cùng độ sáng với màu mặc định là được khuyến nghị giúp cho việc đọc thuận lợi.',
    'recipemenu_color' => 'Màu Giá sách',
    'recipe_color' => 'Màu Sách',
    'chapter_color' => 'Màu Chương',
    'page_color' => 'Màu Trang',
    'page_draft_color' => 'Màu Trang Nháp',

    // Registration Settings
    'reg_settings' => 'Đăng ký',
    'reg_enable' => 'Bật Đăng ký',
    'reg_enable_toggle' => 'Bật đăng ký',
    'reg_enable_desc' => 'Khi đăng ký được bật người dùng sẽ có thể tự đăng ký để trở thành người dùng của ứng dụng. Khi đăng kí người dùng sẽ được cấp một quyền sử dụng mặc định.',
    'reg_default_role' => 'Quyền người dùng sử dụng mặc định sau khi đăng kí',
    'reg_enable_external_warning' => 'Tùy chọn trên bị bỏ qua khi xác thực từ bên ngoài LDAP hoặc SAML được bật. Tài khoản người dùng chưa phải là thành viên sẽ được tự động tạo nếu xác thực với hệ thống bên ngoài thành công.',
    'reg_email_confirmation' => 'Xác nhận Email',
    'reg_email_confirmation_toggle' => 'Yêu cầu xác nhận email',
    'reg_confirm_email_desc' => 'Nếu giới hạn tên miền được sử dụng, xác nhận email là bắt buộc và tùy chọn này sẽ bị bỏ qua.',
    'reg_confirm_restrict_domain' => 'Giới hạn tên miền',
    'reg_confirm_restrict_domain_desc' => 'Điền dấu phẩy ngăn cách danh sách các tên miền email dành cho việc bạn muốn giới hạn đăng nhập. Người dùng sẽ nhận được email xác nhận địa chỉ của họ trước khi được phép tương tác với ứng dụng. <br> Lưu ý rằng người dùng có thể thay đổi địa chỉ email của họ sau khi đăng ký thành công.',
    'reg_confirm_restrict_domain_placeholder' => 'Không có giới hạn nào được thiết lập',

    // Maintenance settings
    'maint' => 'Bảo trì',
    'maint_image_cleanup' => 'Dọn dẹp ảnh',
    'maint_image_cleanup_desc' => 'Quét nội dung trang và phiên bản để kiểm tra xem các ảnh và hình vẽ nào đang được sử dụng và ảnh nào dư thừa. Đảm bảo rằng bạn đã tạo bản sao lưu toàn dữ liệu và ảnh trước khi chạy chức năng này.',
    'maint_delete_images_only_in_revisions' => 'Đồng thời xóa các hình ảnh chỉ tồn tại trong các bản sửa đổi trang cũ',
    'maint_image_cleanup_run' => 'Chạy Dọn dẹp',
    'maint_image_cleanup_warning' => 'Đã tìm thấy :count ảnh có thể không được sử dụng. Bạn muốn chắc rằng muốn xóa các ảnh này?',
    'maint_image_cleanup_success' => ':count ảnh có thể không được sử dụng đã được tìm thấy và xóa!',
    'maint_image_cleanup_nothing_found' => 'Không tìm thấy ảnh nào không được xử dụng, Không có gì để xóa!',
    'maint_send_test_email' => 'Gửi một email thử',
    'maint_send_test_email_desc' => 'Chức năng này gửi một email thử đến địa chỉ email bạn chỉ định trong hồ sơ của mình.',
    'maint_send_test_email_run' => 'Gửi email thử',
    'maint_send_test_email_success' => 'Email đã được gửi đến :address',
    'maint_send_test_email_mail_subject' => 'Thử Email',
    'maint_send_test_email_mail_greeting' => 'Chức năng gửi email có vẻ đã hoạt động!',
    'maint_send_test_email_mail_text' => 'Chúc mừng! Khi bạn nhận được email thông báo này, cài đặt email của bạn có vẻ đã được cấu hình đúng.',
    'maint_recycle_bin_desc' => 'Các menu, công thức nấu ăn đã xóa được gửi vào thùng rác để chúng có thể được khôi phục hoặc xóa vĩnh viễn. Các mục cũ hơn trong thùng rác có thể tự động bị loại bỏ sau một thời gian tùy thuộc vào cấu hình hệ thống.',
    'maint_recycle_bin_open' => 'Mở Thùng Rác',

    // Recycle Bin
    'recycle_bin' => 'Thùng Rác',
    'recycle_bin_desc' => 'Tại đây bạn có thể khôi phục các mục đã bị xóa hoặc chọn xóa vĩnh viễn khỏi hệ thống. Danh sách này không được lọc không giống như danh sách hoạt động tương tự trong hệ thống áp dụng bộ lọc quyền.',
    'recycle_bin_deleted_item' => 'Mục Đã Xóa',
    'recycle_bin_deleted_parent' => 'Parent',
    'recycle_bin_deleted_by' => 'Xóa Bởi',
    'recycle_bin_deleted_at' => 'Thời điểm Xóa',
    'recycle_bin_permanently_delete' => 'Xóa Vĩnh viễn',
    'recycle_bin_restore' => 'Khôi phục',
    'recycle_bin_contents_empty' => 'Thùng rác hiện đang trống',
    'recycle_bin_empty' => 'Dọn dẹp Thùng Rác',
    'recycle_bin_empty_confirm' => 'Thao tác này sẽ hủy vĩnh viễn tất cả các mục trong thùng rác bao gồm cả nội dung chứa trong mỗi mục. Bạn có chắc chắn muốn dọn sạch thùng rác không?',
    'recycle_bin_destroy_confirm' => 'Hành động này sẽ xóa vĩnh viễn mục này, cùng với bất kỳ phần tử con nào được liệt kê bên dưới, khỏi hệ thống và bạn sẽ không thể khôi phục nội dung này. Bạn có chắc chắn muốn xóa vĩnh viễn mục này không?',
    'recycle_bin_destroy_list' => 'Items to be Destroyed',
    'recycle_bin_restore_list' => 'Các mục cần được khôi phục',
    'recycle_bin_restore_confirm' => 'Hành động này sẽ khôi phục mục đã xóa, bao gồm mọi phần tử con, về vị trí ban đầu của chúng. Nếu vị trí ban đầu đã bị xóa và hiện nằm trong thùng rác, thì mục chính cũng sẽ cần được khôi phục.',
    'recycle_bin_restore_deleted_parent' => 'Mục gốc của mục này cũng đã bị xóa. Những thứ này sẽ vẫn bị xóa cho đến khi trang gốc đó cũng được khôi phục.',
    'recycle_bin_restore_parent' => 'Restore Parent',
    'recycle_bin_destroy_notification' => 'Đã xóa :count tổng số mục từ thùng rác.',
    'recycle_bin_restore_notification' => 'Đã khôi phục :count tổng số mục từ thùng rác.',

    // Audit Log
    'audit' => 'Audit Log',
    'audit_desc' => 'Nhật ký kiểm tra này hiển thị danh sách các hoạt động được theo dõi trong hệ thống. Danh sách này không được lọc không giống như danh sách hoạt động tương tự trong hệ thống áp dụng bộ lọc quyền.',
    'audit_event_filter' => 'Event Filter',
    'audit_event_filter_no_filter' => 'Không Lọc',
    'audit_deleted_item' => 'Mục Đã Xóa',
    'audit_deleted_item_name' => 'Tên: :name',
    'audit_table_user' => 'Người dùng',
    'audit_table_event' => 'Sự kiện',
    'audit_table_related' => 'Mục hoặc Chi tiết liên quan',
    'audit_table_ip' => 'IP Address',
    'audit_table_date' => 'Ngày hoạt động',
    'audit_date_from' => 'Ngày từ khoảng',
    'audit_date_to' => 'Ngày đến khoảng',

    // Role Settings
    'roles' => 'Quyền',
    'role_user_roles' => 'Quyền người dùng',
    'role_create' => 'Tạo quyền mới',
    'role_create_success' => 'Quyền mới đã được tạo thành công',
    'role_delete' => 'Xóa quyền',
    'role_delete_confirm' => 'Chức năng này sẽ xóa quyền với tên \':roleName\'.',
    'role_delete_users_assigned' => 'Quyền này có :userCount người dùng được gán. Nếu bạn muốn di dời các người dùng từ quyền này hãy chọn một quyền mới bên dưới.',
    'role_delete_no_migration' => "Không di dời các người dùng",
    'role_delete_sure' => 'Bạn có chắc rằng muốn xóa quyền này?',
    'role_delete_success' => 'Quyền đã được xóa thành công',
    'role_edit' => 'Sửa quyền',
    'role_details' => 'Thông tin chi tiết Quyền',
    'role_name' => 'Tên quyền',
    'role_desc' => 'Thông tin vắn tắt của Quyền',
    'role_mfa_enforced' => 'Yêu cầu xác thực đa yếu tố',
    'role_external_auth_id' => 'Mã của xác thực ngoài',
    'role_system' => 'Quyền Hệ thống',
    'role_manage_users' => 'Quản lý người dùng',
    'role_manage_roles' => 'Quản lý quyền và chức năng quyền',
    'role_manage_entity_permissions' => 'Quản lý tất cả quyền của các sách, chương & trang',
    'role_manage_own_entity_permissions' => 'Quản lý quyền trên sách, chương & trang bạn tạo ra',
    'role_manage_page_templates' => 'Quản lý các mẫu trang',
    'role_access_api' => 'Truy cập đến API hệ thống',
    'role_manage_settings' => 'Quản lý cài đặt của ứng dụng',
    'role_export_content' => 'Export content',
    'role_asset' => 'Quyền tài sản (asset)',
    'roles_system_warning' => 'Lưu ý rằng quyền truy cập vào bất kỳ quyền nào trong ba quyền ở trên có thể cho phép người dùng thay đổi đặc quyền của chính họ hoặc đặc quyền của những người khác trong hệ thống. Chỉ gán các vai trò với các quyền này cho người dùng đáng tin cậy.',
    'role_asset_desc' => 'Các quyền này điều khiển truy cập mặc định tới tài sản (asset) nằm trong hệ thống. Quyền tại Sách, Chường và Trang se ghi đè các quyền này.',
    'role_asset_admins' => 'Quản trị viên được tự động cấp quyền truy cập đến toàn bộ nội dung, tuy nhiên các tùy chọn đó có thể hiện hoặc ẩn tùy chọn giao diện.',
    'role_all' => 'Tất cả',
    'role_own' => 'Sở hữu',
    'role_controlled_by_asset' => 'Kiểm soát các tài sản (asset) người dùng tải lên',
    'role_save' => 'Lưu Quyền',
    'role_update_success' => 'Quyền đã được cập nhật thành công',
    'role_users' => 'Người dùng được gán quyền này',
    'role_users_none' => 'Không có người dùng nào hiện được gán quyền này',

    // Users
    'users' => 'Người dùng',
    'user_profile' => 'Hồ sơ người dùng',
    'users_add_new' => 'Thêm người dùng mới',
    'users_search' => 'Tìm kiếm người dùng',
    'users_latest_activity' => 'Hoạt động mới nhất',
    'users_details' => 'Chi tiết người dùng',
    'users_details_desc' => 'Hiển thị tên và địa chỉ email cho người dùng này. Địa chỉ email sẽ được sử dụng để đăng nhập vào ứng dụng.',
    'users_details_desc_no_email' => 'Đặt tên cho người dùng này để giúp người dùng khác nhận ra họ.',
    'users_role' => 'Quyền người dùng',
    'users_role_desc' => 'Chọn quyền mà người dùng sẽ được gán. Nếu người dùng được gán nhiều quyền, các quyền hạn sẽ ghi đè lên nhau và họ sẽ nhận được tất cả các quyền hạn từ quyền được gán.',
    'users_password' => 'Mật khẩu người dùng',
    'users_password_desc' => 'Đặt mật khẩu dùng để đăng nhập ứng dụng. Nó phải có độ dài tối thiểu 6 ký tự.',
    'users_send_invite_text' => 'Bạn có thể chọn để gửi cho người dùng này một email mời, giúp họ có thể tự đặt mật khẩu cho chính họ. Nếu không bạn có thể đặt mật khẩu cho họ.',
    'users_send_invite_option' => 'Gửi email mời người dùng',
    'users_external_auth_id' => 'Mã của xác thực ngoài',
    'users_external_auth_id_desc' => 'Đây là mã được sử dụng để xác thực với người dùng này khi giao tiếp với hệ thống xác thực bên ngoài.',
    'users_password_warning' => 'Chỉ điền ô bên dưới nếu bạn muốn thay đổi mật khẩu.',
    'users_system_public' => 'Người dùng này đại diện cho bất kỳ khách nào thăm trang của bạn. Nó được tự động gán và không thể dùng để đăng nhập.',
    'users_delete' => 'Xóa Người dùng',
    'users_delete_named' => 'Xóa người dùng :userName',
    'users_delete_warning' => 'Chức năng này sẽ hoàn toàn xóa người dùng với tên \':userName\' từ hệ thống.',
    'users_delete_confirm' => 'Bạn có chắc muốn xóa người dùng này không?',
    'users_migrate_ownership' => 'Migrate Ownership',
    'users_migrate_ownership_desc' => 'Chọn một người dùng tại đây nếu bạn muốn một người dùng khác trở thành chủ sở hữu của tất cả các mặt hàng hiện do người dùng này sở hữu.',
    'users_none_selected' => 'Chưa chọn người dùng',
    'users_delete_success' => 'Người dùng đã được xóa thành công',
    'users_edit' => 'Sửa người dùng',
    'users_edit_profile' => 'Sửa Hồ Sơ',
    'users_edit_success' => 'Người dùng được cập nhật thành công',
    'users_avatar' => 'Ảnh đại diện',
    'users_avatar_desc' => 'Chọn ảnh đê đại hiện cho người dùng này. Ảnh nên có kích cỡ hình vuông 256px.',
    'users_preferred_language' => 'Ngôn ngữ ưu tiên',
    'users_preferred_language_desc' => 'Tùy chọn này sẽ thay đổi ngôn ngư sử dụng cho giao diện người dùng của ứng dụng. Nó sẽ không ảnh hưởng đến bất cứ nội dung nào người dùng tạo ra.',
    'users_social_accounts' => 'Tài khoản MXH',
    'users_social_accounts_info' => 'Bạn có thể kết nối đến các tài khoản khác để đăng nhập nhanh chóng và dễ dàng. Ngắt kết nối đến một tài khoản ở đây không thu hồi việc ủy quyền truy cập trước đó. Thu hồi truy cập của các tài khoản kết nối MXH từ trang cài đặt hồ sở của bạn.',
    'users_social_connect' => 'Kết nối tài khoản',
    'users_social_disconnect' => 'Ngắt kết nối tài khoản',
    'users_social_connected' => 'Tài khoản :socialAccount đã được liên kết với hồ sơ của bạn thành công.',
    'users_social_disconnected' => 'Tài khoản :socialAccount đã được ngắt kết nối khỏi hồ sơ của bạn thành công.',
    'users_api_tokens' => 'Các Token API',
    'users_api_tokens_none' => 'Không có Token API nào được tạo cho người dùng này',
    'users_api_tokens_create' => 'Tạo Token',
    'users_api_tokens_expires' => 'Hết hạn',
    'users_api_tokens_docs' => 'Tài liệu API',
    'users_mfa' => 'Xác thực đa yếu tố',
    'users_mfa_desc' => 'Thiết lập xác thực đa yếu tố như một lớp bảo mật bổ sung cho tài khoản người dùng của bạn.',
    'users_mfa_x_methods' => ':count các phương pháp được cấu hình|:count các phương pháp được cấu hình',
    'users_mfa_configure' => 'Định cấu hình các phương pháp',

    // API Tokens
    'user_api_token_create' => 'Tạo Token API',
    'user_api_token_name' => 'Tên',
    'user_api_token_name_desc' => 'Đặt cho token của bạn một tên dễ đọc để nhắc nhở mục đích sử dụng của nó trong tương lai.',
    'user_api_token_expiry' => 'Ngày hết hạn',
    'user_api_token_expiry_desc' => 'Đặt một ngày hết hạn cho token này. Sau ngày này, các yêu cầu được tạo khi sử dụng token này sẽ không còn hoạt động. Để trống trường này sẽ đặt ngày hết hạn sau 100 năm tới.',
    'user_api_token_create_secret_message' => 'Ngay sau khi tạo token này một "Mã Token" & "Mật khẩu Token" sẽ được tạo và hiển thị. Mật khẩu sẽ chỉ được hiện một lần duy nhất nên hãy chắc rằng bạn sao lưu giá trị của nó ở nơi an toàn và bảo mật trước khi tiếp tục.',
    'user_api_token_create_success' => 'Token API đã được tạo thành công',
    'user_api_token_update_success' => 'Token API đã được cập nhật thành công',
    'user_api_token' => 'Token API',
    'user_api_token_id' => 'Mã Token',
    'user_api_token_id_desc' => 'Đây là hệ thống sinh ra định danh không thể chỉnh sửa cho token này, thứ mà sẽ cần phải cung cấp khi yêu cầu API.',
    'user_api_token_secret' => 'Mật khẩu Token',
    'user_api_token_secret_desc' => 'Đây là mật khẩu được hệ thống tạo ra cho token để phục vụ cho các yêu cầu API này. Nó sẽ chỉ được hiển thị một lần duy nhất nên hãy sao lưu nó vào nơi nào đó an toàn và bảo mật.',
    'user_api_token_created' => 'Token được tạo :timeAgo',
    'user_api_token_updated' => 'Token được cập nhật :timeAgo',
    'user_api_token_delete' => 'Xóa Token',
    'user_api_token_delete_warning' => 'Chức năng này sẽ hoàn toàn xóa token API với tên \':tokenName\' từ hệ thống.',
    'user_api_token_delete_confirm' => 'Bạn có chắc rằng muốn xóa token API này?',
    'user_api_token_delete_success' => 'Token API đã được xóa thành công',

    //report
    'report_list' => 'Danh sách báo cáo',
    'report_detail' => 'Dưới đây là danh sách (các) công thức được báo cáo, Bạn sẽ quyết định có nên giữ lại hay không',
    'sort_by_date'=>'Lọc theo ngày',
    'sort_by_recipe'=>'Sắp xếp theo công thức',
    'status'=>'Trạng thái',
    'title'=>'Title',
    'content'=>'Nội dung',
    'created_by'=>'Được tạo bởi',
    'created_date'=>'ngày tạo ra',
    'active_deactive'=>'Active/Deactive',





    //! If editing translations files directly please ignore this in all
    //! languages apart from en. Content will be auto-copied from en.
    //!////////////////////////////////
    'language_select' => [
        'en' => 'English',
        'vi' => 'Tiếng Việt',

    ],
    //!////////////////////////////////
];
