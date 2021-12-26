<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Books, Menus, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => 'Nyligen skapat',
    'recently_created_pages' => 'Sidor som skapats nyligen',
    'recently_updated_pages' => 'Sidor som uppdaterats nyligen',
    'recently_created_chapters' => 'Kapitel som skapats nyligen',
    'recently_created_recipes' => 'Böcker som skapats nyligen',
    'recently_created_menus' => 'Hyllor som skapats nyligen',
    'recently_update' => 'Nyligen uppdaterat',
    'recently_viewed' => 'Nyligen läst',
    'recent_activity' => 'Aktivitet',
    'create_now' => 'Skapa en nu',
    'revisions' => 'Revisioner',
    'meta_revision' => 'Revisions #:revisionCount',
    'meta_created' => 'Skapad :timeLength',
    'meta_created_name' => 'Skapad :timeLength av :user',
    'meta_updated' => 'Uppdaterad :timeLength',
    'meta_updated_name' => 'Uppdaterad :timeLength av :user',
    'meta_owned_name' => 'Ägs av :user',
    'entity_select' => 'Välj enhet',
    'images' => 'Bilder',
    'my_recent_drafts' => 'Mina nyaste utkast',
    'my_recently_viewed' => 'Mina senast visade sidor',
    'my_most_viewed_favourites' => 'Mina mest visade favoriter',
    'my_favourites' => 'Mina favoriter',
    'no_pages_viewed' => 'Du har inte visat några sidor',
    'no_pages_recently_created' => 'Inga sidor har skapats nyligen',
    'no_pages_recently_updated' => 'Inga sidor har uppdaterats nyligen',
    'export' => 'Exportera',
    'export_html' => 'Webb-fil',
    'export_pdf' => 'PDF-fil',
    'export_text' => 'Textfil',
    'export_md' => 'Markdown File',

    // Permissions and restrictions
    'permissions' => 'Rättigheter',
    'permissions_intro' => 'Dessa rättigheter kommer att överskrida eventuella rollbaserade rättigheter.',
    'permissions_enable' => 'Aktivera anpassade rättigheter',
    'permissions_save' => 'Spara rättigheter',
    'permissions_owner' => 'Ägare',

    // Search
    'search_results' => 'Sökresultat',
    'search_total_results_found' => ':count resultat|:count resultat',
    'search_clear' => 'Rensa sökning',
    'search_no_pages' => 'Inga sidor matchade sökningen',
    'search_for_term' => 'Sök efter :term',
    'search_more' => 'Fler resultat',
    'search_advanced' => 'Avancerad sök',
    'search_terms' => 'Söktermer',
    'search_content_type' => 'Innehållstyp',
    'search_exact_matches' => 'Exakta matchningar',
    'search_tags' => 'Taggar',
    'search_options' => 'Alternativ',
    'search_viewed_by_me' => 'Visade av mig',
    'search_not_viewed_by_me' => 'Ej visade av mig',
    'search_permissions_set' => 'Har anpassade rättigheter',
    'search_created_by_me' => 'Skapade av mig',
    'search_updated_by_me' => 'Uppdaterade av mig',
    'search_owned_by_me' => 'Ägs av mig',
    'search_date_options' => 'Datumalternativ',
    'search_updated_before' => 'Uppdaterade före',
    'search_updated_after' => 'Uppdaterade efter',
    'search_created_before' => 'Skapade före',
    'search_created_after' => 'Skapade efter',
    'search_set_date' => 'Ange datum',
    'search_update' => 'Uppdatera sökning',

    // Menus
    'menu' => 'Hylla',
    'menus' => 'Hyllor',
    'x_menus' => ':count hylla|:count hyllor',
    'menus_long' => 'Bokhyllor',
    'menus_empty' => 'Du har inte skapat någon hylla',
    'menus_create' => 'Skapa ny hylla',
    'menus_popular' => 'Populära hyllor',
    'menus_new' => 'Nya hyllor',
    'menus_new_action' => 'Ny hylla',
    'menus_popular_empty' => 'De populäraste hyllorna kommer hamna här',
    'menus_new_empty' => 'De senast skapade hyllorna kommer hamna här',
    'menus_save' => 'Spara hylla',
    'menus_recipes' => 'Böcker i denna hylla',
    'menus_add_recipes' => 'Lägg till böcker till hyllan',
    'menus_drag_recipes' => 'Dra böcker hit för att lägga dem på hyllan',
    'menus_empty_contents' => 'Denna hylla har inga böcker än',
    'menus_edit_and_assign' => 'Redigera hyllan för att lägga till böcker',
    'menus_edit_named' => 'Ändra hyllan :name',
    'menus_edit' => 'Ändra bokhylla',
    'menus_delete' => 'Radera bokhylla',
    'menus_delete_named' => 'Radera bokhyllan :name',
    'menus_delete_explain' => "Detta kommer radera bokhyllan ':name'. Böckerna på hyllan kommer finnas kvar.",
    'menus_delete_confirmation' => 'Är du säker på att du vill radera hyllan?',
    'menus_permissions' => 'Bokhyllerättigheter',
    'menus_permissions_updated' => 'Bokhyllerättigheterna har ändrats',
    'menus_permissions_active' => 'Bokhyllerättigheterna är aktiva',
    'menus_permissions_cascade_warning' => 'Permissions on recipemenus do not automatically cascade to contained recipes. This is because a recipe can exist on multiple menus. Permissions can however be copied down to child recipes using the option found below.',
    'menus_copy_permissions_to_recipes' => 'Kopiera rättigheter till böcker',
    'menus_copy_permissions' => 'Kopiera rättigheter',
    'menus_copy_permissions_explain' => 'Detta kommer kopiera hyllans rättigheter till alla böcker på den. Se till att du har sparat alla ändringar innan du går vidare.',
    'menus_copy_permission_success' => 'Hyllans rättigheter har kopierats till :count böcker',

    // Books
    'recipe' => 'Bok',
    'recipes' => 'Böcker',
    'x_recipes' => ':count bok|:count böcker',
    'recipes_empty' => 'Inga böcker har skapats',
    'recipes_popular' => 'Populära böcker',
    'recipes_recent' => 'Nya böcker',
    'recipes_new' => 'Nya böcker',
    'recipes_new_action' => 'Ny bok',
    'recipes_popular_empty' => 'De mest populära böckerna kommer att visas här.',
    'recipes_new_empty' => 'De senaste böckerna som skapats kommer att visas här.',
    'recipes_create' => 'Skapa ny bok',
    'recipes_delete' => 'Ta bort bok',
    'recipes_delete_named' => 'Ta bort boken :recipeName',
    'recipes_delete_explain' => 'Du håller på att ta bort boken \':recipeName\'. Alla sidor och kapitel kommer också att tas bort.',
    'recipes_delete_confirmation' => 'Är du säker på att du vill ta bort boken?',
    'recipes_edit' => 'Redigera bok',
    'recipes_edit_named' => 'Redigera bok :recipeName',
    'recipes_form_recipe_name' => 'Bokens namn',
    'recipes_save' => 'Spara bok',
    'recipes_permissions' => 'Rättigheter för boken',
    'recipes_permissions_updated' => 'Bokens rättigheter har uppdaterats',
    'recipes_empty_contents' => 'Det finns inga sidor eller kapitel i den här boken.',
    'recipes_empty_create_page' => 'Skapa en ny sida',
    'recipes_empty_sort_current_recipe' => 'Sortera aktuell bok',
    'recipes_empty_add_chapter' => 'Lägg till kapitel',
    'recipes_permissions_active' => 'Anpassade rättigheter är i bruk',
    'recipes_search_this' => 'Sök i boken',
    'recipes_navigation' => 'Navigering',
    'recipes_sort' => 'Sortera bokens innehåll',
    'recipes_sort_named' => 'Sortera boken :recipeName',
    'recipes_sort_name' => 'Sortera utifrån namn',
    'recipes_sort_created' => 'Sortera utifrån skapelse',
    'recipes_sort_updated' => 'Sortera utifrån uppdatering',
    'recipes_sort_chapters_first' => 'Kapitel först',
    'recipes_sort_chapters_last' => 'Kapitel sist',
    'recipes_sort_show_other' => 'Visa andra böcker',
    'recipes_sort_save' => 'Spara ordning',

    // Chapters
    'chapter' => 'Kapitel',
    'chapters' => 'Kapitel',
    'x_chapters' => ':count kapitel|:count kapitel',
    'chapters_popular' => 'Populära kapitel',
    'chapters_new' => 'Nytt kapitel',
    'chapters_create' => 'Skapa nytt kapitel',
    'chapters_delete' => 'Radera kapitel',
    'chapters_delete_named' => 'Radera kapitlet :chapterName',
    'chapters_delete_explain' => 'Detta kommer att ta bort kapitlet med namnet \':chapterName\'. Alla sidor som finns inom detta kapitel kommer också att raderas.',
    'chapters_delete_confirm' => 'Är du säker på att du vill ta bort det här kapitlet?',
    'chapters_edit' => 'Redigera kapitel',
    'chapters_edit_named' => 'Redigera kapitel :chapterName',
    'chapters_save' => 'Spara kapitel',
    'chapters_move' => 'Flytta kapitel',
    'chapters_move_named' => 'Flytta kapitel :chapterName',
    'chapter_move_success' => 'Kapitel flyttat till :recipeName',
    'chapters_permissions' => 'Rättigheter för kapitel',
    'chapters_empty' => 'Det finns inga sidor i det här kapitlet.',
    'chapters_permissions_active' => 'Anpassade rättigheter är i bruk',
    'chapters_permissions_success' => 'Rättigheterna för kapitlet har uppdaterats',
    'chapters_search_this' => 'Sök i detta kapitel',

    // Pages
    'page' => 'Sida',
    'pages' => 'Sidor',
    'x_pages' => ':count sida|:count sidor',
    'pages_popular' => 'Populära sidor',
    'pages_new' => 'Ny sida',
    'pages_attachments' => 'Bilagor',
    'pages_navigation' => 'Navigering',
    'pages_delete' => 'Ta bort sida',
    'pages_delete_named' => 'Ta bort sidan :pageName',
    'pages_delete_draft_named' => 'Ta bort utkastet :pageName',
    'pages_delete_draft' => 'Ta bort utkast',
    'pages_delete_success' => 'Sidan har tagits bort',
    'pages_delete_draft_success' => 'Utkastet har tagits bort',
    'pages_delete_confirm' => 'Är du säker på att du vill ta bort den här sidan?',
    'pages_delete_draft_confirm' => 'Är du säker på att du vill ta bort det här utkastet?',
    'pages_editing_named' => 'Redigerar sida :pageName',
    'pages_edit_draft_options' => 'Inställningar för utkast',
    'pages_edit_save_draft' => 'Spara utkast',
    'pages_edit_draft' => 'Redigera utkast',
    'pages_editing_draft' => 'Redigerar utkast',
    'pages_editing_page' => 'Redigerar sida',
    'pages_edit_draft_save_at' => 'Utkastet sparades ',
    'pages_edit_delete_draft' => 'Ta bort utkast',
    'pages_edit_discard_draft' => 'Ta bort utkastet',
    'pages_edit_set_changelog' => 'Beskriv dina ändringar',
    'pages_edit_enter_changelog_desc' => 'Ange en kort beskrivning av de ändringar du har gjort',
    'pages_edit_enter_changelog' => 'Ändringslogg',
    'pages_save' => 'Spara sida',
    'pages_title' => 'Sidtitel',
    'pages_name' => 'Sidans namn',
    'pages_md_editor' => 'Redigerare',
    'pages_md_preview' => 'Förhandsvisa',
    'pages_md_insert_image' => 'Infoga bild',
    'pages_md_insert_link' => 'Infoga länk',
    'pages_md_insert_drawing' => 'Infoga teckning',
    'pages_not_in_chapter' => 'Sidan ligger inte i något kapitel',
    'pages_move' => 'Flytta sida',
    'pages_move_success' => 'Sidan har flyttats till ":parentName"',
    'pages_copy' => 'Kopiera sida',
    'pages_copy_desination' => 'Destination',
    'pages_copy_success' => 'Sidan har kopierats',
    'pages_permissions' => 'Rättigheter för sida',
    'pages_permissions_success' => 'Rättigheterna för sidan har uppdaterats',
    'pages_revision' => 'Revidering',
    'pages_revisions' => 'Sidrevisioner',
    'pages_revisions_named' => 'Sidrevisioner för :pageName',
    'pages_revision_named' => 'Sidrevision för :pageName',
    'pages_revision_restored_from' => 'Återställd från #:id; :summary',
    'pages_revisions_created_by' => 'Skapad av',
    'pages_revisions_date' => 'Revisionsdatum',
    'pages_revisions_number' => '#',
    'pages_revisions_numbered' => 'Revisions #:id',
    'pages_revisions_numbered_changes' => 'Revision #:id ändringar',
    'pages_revisions_changelog' => 'Ändringslogg',
    'pages_revisions_changes' => 'Ändringar',
    'pages_revisions_current' => 'Nuvarande version',
    'pages_revisions_preview' => 'Förhandsgranska',
    'pages_revisions_restore' => 'Återställ',
    'pages_revisions_none' => 'Sidan har inga revisioner',
    'pages_copy_link' => 'Kopiera länk',
    'pages_edit_content_link' => 'Redigera innehåll',
    'pages_permissions_active' => 'Anpassade rättigheter är i bruk',
    'pages_initial_revision' => 'Första publicering',
    'pages_initial_name' => 'Ny sida',
    'pages_editing_draft_notification' => 'Du redigerar just nu ett utkast som senast sparades :timeDiff.',
    'pages_draft_edited_notification' => 'Denna sida har uppdaterats sen dess. Vi rekommenderar att du förkastar dina ändringar.',
    'pages_draft_page_changed_since_creation' => 'This page has been updated since this draft was created. It is recommended that you discard this draft or take care not to overwrite any page changes.',
    'pages_draft_edit_active' => [
        'start_a' => ':count har börjat redigera den här sidan',
        'start_b' => ':userName har börjat redigera den här sidan',
        'time_a' => 'sedan sidan senast uppdaterades',
        'time_b' => 'under de senaste :minCount minuterna',
        'message' => ':start :time. Var försiktiga så att ni inte skriver över varandras ändringar!',
    ],
    'pages_draft_discarded' => 'Utkastet har tagits bort. Redigeringsverktyget har uppdaterats med aktuellt innehåll.',
    'pages_specific' => 'Specifik sida',
    'pages_is_template' => 'Sidmall',

    // Editor Sidebar
    'page_tags' => 'Sidtaggar',
    'chapter_tags' => 'Kapiteltaggar',
    'recipe_tags' => 'Boktaggar',
    'menu_tags' => 'Hylltaggar',
    'tag' => 'Tagg',
    'tags' =>  'Taggar',
    'tag_name' =>  'Etikettnamn',
    'tag_value' => 'Taggvärde (Frivilligt)',
    'tags_explain' => "Lägg till taggar för att kategorisera ditt innehåll bättre. \n Du kan tilldela ett värde till en tagg för ännu bättre organisering.",
    'tags_add' => 'Lägg till ännu en tagg',
    'tags_remove' => 'Ta bort denna etikett',
    'tags_usages' => 'Total tag usages',
    'tags_assigned_pages' => 'Assigned to Pages',
    'tags_assigned_chapters' => 'Assigned to Chapters',
    'tags_assigned_recipes' => 'Assigned to Books',
    'tags_assigned_menus' => 'Assigned to Menus',
    'tags_x_unique_values' => ':count unique values',
    'tags_all_values' => 'All values',
    'tags_view_tags' => 'View Tags',
    'tags_view_existing_tags' => 'View existing tags',
    'tags_list_empty_hint' => 'Tags can be assigned via the page editor sidebar or while editing the details of a recipe, chapter or menu.',
    'attachments' => 'Bilagor',
    'attachments_explain' => 'Ladda upp filer eller bifoga länkar till ditt innehåll. Dessa visas i sidokolumnen.',
    'attachments_explain_instant_save' => 'Ändringar här sparas omgående.',
    'attachments_items' => 'Bifogat innehåll',
    'attachments_upload' => 'Ladda upp fil',
    'attachments_link' => 'Bifoga länk',
    'attachments_set_link' => 'Ange länk',
    'attachments_delete' => 'Är du säker på att du vill ta bort bilagan?',
    'attachments_dropzone' => 'Släpp filer här eller klicka för att ladda upp',
    'attachments_no_files' => 'Inga filer har laddats upp',
    'attachments_explain_link' => 'Du kan bifoga en länk om du inte vill ladda upp en fil. Detta kan vara en länk till en annan sida eller till en fil i molnet.',
    'attachments_link_name' => 'Länknamn',
    'attachment_link' => 'Länk till bilaga',
    'attachments_link_url' => 'Länk till fil',
    'attachments_link_url_hint' => 'URL till sida eller fil',
    'attach' => 'Bifoga',
    'attachments_insert_link' => 'Lägg till bilagelänk till sida',
    'attachments_edit_file' => 'Redigera fil',
    'attachments_edit_file_name' => 'Filnamn',
    'attachments_edit_drop_upload' => 'Släpp filer här eller klicka för att ladda upp och skriva över',
    'attachments_order_updated' => 'Ordningen på bilagorna har uppdaterats',
    'attachments_updated_success' => 'Bilagan har uppdaterats',
    'attachments_deleted' => 'Bilagan har tagits bort',
    'attachments_file_uploaded' => 'Filen har laddats upp',
    'attachments_file_updated' => 'Filen har uppdaterats',
    'attachments_link_attached' => 'Länken har bifogats till sidan',
    'templates' => 'Mallar',
    'templates_set_as_template' => 'Sidan är en mall',
    'templates_explain_set_as_template' => 'Du kan använda denna sida som en mall så att dess innehåll kan användas när du skapar andra sidor. Andra användare kommer att kunna använda denna mall om de har visningsrättigheter för den här sidan.',
    'templates_replace_content' => 'Ersätt sidinnehåll',
    'templates_append_content' => 'Lägg till till sidans innehåll',
    'templates_prepend_content' => 'Lägg till före sidans innehåll',

    // Profile View
    'profile_user_for_x' => 'Användare i :time',
    'profile_created_content' => 'Skapat innehåll',
    'profile_not_created_pages' => ':userName har inte skapat några sidor',
    'profile_not_created_chapters' => ':userName har inte skapat några kapitel',
    'profile_not_created_recipes' => ':userName har inte skapat några böcker',
    'profile_not_created_menus' => ':userName har inte skapat några hyllor',

    // Comments
    'comment' => 'Kommentar',
    'comments' => 'Kommentarer',
    'comment_add' => 'Lägg till kommentar',
    'comment_placeholder' => 'Lämna en kommentar här',
    'comment_count' => '{0} Inga kommentarer|{1} 1 kommentar|[2,*] :count kommentarer',
    'comment_save' => 'Spara kommentar',
    'comment_saving' => 'Sparar kommentar...',
    'comment_deleting' => 'Tar bort kommentar...',
    'comment_new' => 'Ny kommentar',
    'comment_created' => 'kommenterade :createDiff',
    'comment_updated' => 'Uppdaterade :updateDiff av :username',
    'comment_deleted_success' => 'Kommentar borttagen',
    'comment_created_success' => 'Kommentaren har sparats',
    'comment_updated_success' => 'Kommentaren har uppdaterats',
    'comment_delete_confirm' => 'Är du säker på att du vill ta bort den här kommentaren?',
    'comment_in_reply_to' => 'Som svar på :commentId',

    // Revision
    'revision_delete_confirm' => 'Är du säker på att du vill radera den här versionen?',
    'revision_restore_confirm' => 'Är du säker på att du vill använda denna revision? Det nuvarande innehållet kommer att ersättas.',
    'revision_delete_success' => 'Revisionen raderad',
    'revision_cannot_delete_latest' => 'Det går inte att ta bort den senaste versionen.',
];
