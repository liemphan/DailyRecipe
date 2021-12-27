<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Recipes, Menus, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => 'Nedávno vytvořené',
    'recently_created_pages' => 'Nedávno vytvořené stránky',
    'recently_updated_pages' => 'Nedávno aktualizované stránky',
    'recently_created_chapters' => 'Nedávno vytvořené kapitoly',
    'recently_created_recipes' => 'Nedávno vytvořené knihy',
    'recently_created_menus' => 'Nedávno vytvořené knihovny',
    'recently_update' => 'Nedávno aktualizované',
    'recently_viewed' => 'Nedávno zobrazené',
    'recent_activity' => 'Nedávné aktivity',
    'create_now' => 'Vytvořit nyní',
    'revisions' => 'Revize',
    'meta_revision' => 'Revize č. :revisionCount',
    'meta_created' => 'Vytvořeno :timeLength',
    'meta_created_name' => 'Vytvořeno :timeLength uživatelem :user',
    'meta_updated' => 'Aktualizováno :timeLength',
    'meta_updated_name' => 'Aktualizováno :timeLength uživatelem :user',
    'meta_owned_name' => 'Vlastník :user',
    'entity_select' => 'Výběr entity',
    'images' => 'Obrázky',
    'my_recent_drafts' => 'Mé nedávné koncepty',
    'my_recently_viewed' => 'Mé nedávno zobrazené',
    'my_most_viewed_favourites' => 'Mé nejčastěji zobrazené oblíbené',
    'my_favourites' => 'Mé oblíbené',
    'no_pages_viewed' => 'Nezobrazili jste žádné stránky',
    'no_pages_recently_created' => 'Žádné nedávno vytvořené stránky',
    'no_pages_recently_updated' => 'Žádné nedávno aktualizované stránky',
    'export' => 'Exportovat',
    'export_html' => 'HTML stránka s celým obsahem',
    'export_pdf' => 'PDF dokument',
    'export_text' => 'Textový soubor',
    'export_md' => 'Markdown',

    // Permissions and restrictions
    'permissions' => 'Oprávnění',
    'permissions_intro' => 'Pokud je povoleno, tato oprávnění budou mít přednost před všemi nastavenými oprávněními role.',
    'permissions_enable' => 'Povolit vlastní oprávnění',
    'permissions_save' => 'Uložit oprávnění',
    'permissions_owner' => 'Vlastník',

    // Search
    'search_results' => 'Výsledky hledání',
    'search_total_results_found' => '{1}Nalezen :count výsledek|[2,4]Nalezeny :count výsledky|[5,*]Nalezeno :count výsledků',
    'search_clear' => 'Vymazat hledání',
    'search_no_pages' => 'Tomuto hledání neodpovídají žádné stránky',
    'search_for_term' => 'Hledat :term',
    'search_more' => 'Další výsledky',
    'search_advanced' => 'Rozšířené hledání',
    'search_terms' => 'Hledané výrazy',
    'search_content_type' => 'Typ obsahu',
    'search_exact_matches' => 'Přesné shody',
    'search_tags' => 'Hledat štítky',
    'search_options' => 'Možnosti',
    'search_viewed_by_me' => 'Zobrazeno mnou',
    'search_not_viewed_by_me' => 'Nezobrazeno mnou',
    'search_permissions_set' => 'Sada oprávnění',
    'search_created_by_me' => 'Vytvořeno mnou',
    'search_updated_by_me' => 'Aktualizováno mnou',
    'search_owned_by_me' => 'Patřící mně',
    'search_date_options' => 'Možnosti data',
    'search_updated_before' => 'Aktualizováno před',
    'search_updated_after' => 'Aktualizováno po',
    'search_created_before' => 'Vytvořeno před',
    'search_created_after' => 'Vytvořeno po',
    'search_set_date' => 'Nastavit datum',
    'search_update' => 'Aktualizovat hledání',

    // Menus
    'menu' => 'Knihovna',
    'menus' => 'Knihovny',
    'x_menus' => '{0}:count knihoven|{1}:count knihovna|[2,4]:count knihovny|[5,*]:count knihoven',
    'menus_long' => 'Knihovny',
    'menus_empty' => 'Nebyly vytvořeny žádné knihovny',
    'menus_create' => 'Vytvořit novou knihovnu',
    'menus_popular' => 'Populární knihovny',
    'menus_new' => 'Nové knihovny',
    'menus_new_action' => 'Nová Knihovna',
    'menus_popular_empty' => 'Nejpopulárnější knihovny se objeví zde.',
    'menus_new_empty' => 'Zde se zobrazí nejnověji vytvořené knihovny.',
    'menus_save' => 'Uložit knihovnu',
    'menus_recipes' => 'Knihy v této knihovně',
    'menus_add_recipes' => 'Přidat knihy do knihovny',
    'menus_drag_recipes' => 'Knihu přidáte jejím přetažením sem',
    'menus_empty_contents' => 'Tato knihovna neobsahuje žádné knihy',
    'menus_edit_and_assign' => 'Upravit knihovnu a přiřadit knihy',
    'menus_edit_named' => 'Upravit knihovnu :name',
    'menus_edit' => 'Upravit knihovnu',
    'menus_delete' => 'Odstranit knihovnu',
    'menus_delete_named' => 'Odstranit knihovnu :name',
    'menus_delete_explain' => "Toto odstraní knihovnu ‚:name‘. Vložené knihy nebudou odstraněny.",
    'menus_delete_confirmation' => 'Opravdu chcete odstranit tuto knihovnu?',
    'menus_permissions' => 'Oprávnění knihovny',
    'menus_permissions_updated' => 'Oprávnění knihovny byla aktualizována',
    'menus_permissions_active' => 'Oprávnění knihovny byla aktivována',
    'menus_permissions_cascade_warning' => 'Oprávnění v Knihovnách nejsou automaticky kaskádována do obsažených knih. To proto, že kniha může existovat ve více Knihovnách. Oprávnění však lze zkopírovat do podřízených knih pomocí níže uvedené možnosti.',
    'menus_copy_permissions_to_recipes' => 'Kopírovat oprávnění na knihy',
    'menus_copy_permissions' => 'Kopírovat oprávnění',
    'menus_copy_permissions_explain' => 'Toto použije aktuální nastavení oprávnění knihovny na všechny knihy v ní obsažené. Před aktivací se ujistěte, že byly uloženy všechny změny oprávnění této knihovny.',
    'menus_copy_permission_success' => 'Oprávnění knihovny byla zkopírována na :count knih',

    // Recipes
    'recipe' => 'Kniha',
    'recipes' => 'Knihy',
    'x_recipes' => '{0}:count knih|{1}:count kniha|[2,4]:count knihy|[5,*]:count knih',
    'recipes_empty' => 'Nebyly vytvořeny žádné knihy',
    'recipes_popular' => 'Oblíbené knihy',
    'recipes_recent' => 'Nedávné knihy',
    'recipes_new' => 'Nové knihy',
    'recipes_new_action' => 'Nová kniha',
    'recipes_popular_empty' => 'Zde se zobrazí nejoblíbenější knihy.',
    'recipes_new_empty' => 'Zde se zobrazí nejnověji vytvořené knihy.',
    'recipes_create' => 'Vytvořit novou knihu',
    'recipes_delete' => 'Odstranit knihu',
    'recipes_delete_named' => 'Odstranit knihu :recipeName',
    'recipes_delete_explain' => 'Toto odstraní knihu ‚:recipeName‘. Všechny stránky a kapitoly v této knize budou také odstraněny.',
    'recipes_delete_confirmation' => 'Opravdu chcete odstranit tuto knihu?',
    'recipes_edit' => 'Upravit knihu',
    'recipes_edit_named' => 'Upravit knihu :recipeName',
    'recipes_form_recipe_name' => 'Název knihy',
    'recipes_save' => 'Uložit knihu',
    'recipes_permissions' => 'Oprávnění knihy',
    'recipes_permissions_updated' => 'Oprávnění knihy byla aktualizována',
    'recipes_empty_contents' => 'Pro tuto knihu nebyly vytvořeny žádné stránky ani kapitoly.',
    'recipes_empty_create_page' => 'Vytvořit novou stránku',
    'recipes_empty_sort_current_recipe' => 'Seřadit aktuální knihu',
    'recipes_empty_add_chapter' => 'Přidat kapitolu',
    'recipes_permissions_active' => 'Oprávnění knihy byla aktivována',
    'recipes_search_this' => 'Prohledat tuto knihu',
    'recipes_navigation' => 'Navigace knihy',
    'recipes_sort' => 'Seřadit obsah knihy',
    'recipes_sort_named' => 'Seřadit knihu :recipeName',
    'recipes_sort_name' => 'Seřadit podle názvu',
    'recipes_sort_created' => 'Seřadit podle data vytvoření',
    'recipes_sort_updated' => 'Seřadit podle data aktualizace',
    'recipes_sort_chapters_first' => 'Kapitoly jako první',
    'recipes_sort_chapters_last' => 'Kapitoly jako poslední',
    'recipes_sort_show_other' => 'Zobrazit ostatní knihy',
    'recipes_sort_save' => 'Uložit nové pořadí',

    // Chapters
    'chapter' => 'Kapitola',
    'chapters' => 'Kapitoly',
    'x_chapters' => '{0}:count Kapitol|{1}:count Kapitola|[2,4]:count Kapitoly|[5,*]:count Kapitol',
    'chapters_popular' => 'Populární kapitoly',
    'chapters_new' => 'Nová kapitola',
    'chapters_create' => 'Vytvořit novou kapitolu',
    'chapters_delete' => 'Odstranit kapitolu',
    'chapters_delete_named' => 'Odstranit kapitolu :chapterName',
    'chapters_delete_explain' => 'Toto odstraní kapitolu ‚:chapterName‘. Všechny stránky v této kapitole budou také odstraněny.',
    'chapters_delete_confirm' => 'Opravdu chcete odstranit tuto kapitolu?',
    'chapters_edit' => 'Upravit kapitolu',
    'chapters_edit_named' => 'Upravit kapitolu :chapterName',
    'chapters_save' => 'Uložit kapitolu',
    'chapters_move' => 'Přesunout kapitolu',
    'chapters_move_named' => 'Přesunout kapitolu :chapterName',
    'chapter_move_success' => 'Kapitola přesunuta do knihy :recipeName',
    'chapters_permissions' => 'Oprávnění kapitoly',
    'chapters_empty' => 'Tato kapitola neobsahuje žádné stránky',
    'chapters_permissions_active' => 'Oprávnění kapitoly byla aktivována',
    'chapters_permissions_success' => 'Oprávnění kapitoly byla aktualizována',
    'chapters_search_this' => 'Prohledat tuto kapitolu',

    // Pages
    'page' => 'Stránka',
    'pages' => 'Stránky',
    'x_pages' => '{0}:count Stran|{1}:count Strana|[2,4]:count Strany|[5,*]:count Stran',
    'pages_popular' => 'Populární stránky',
    'pages_new' => 'Nová stránka',
    'pages_attachments' => 'Přílohy',
    'pages_navigation' => 'Obsah stránky',
    'pages_delete' => 'Odstranit stránku',
    'pages_delete_named' => 'Odstranit stránku :pageName',
    'pages_delete_draft_named' => 'Odstranit koncept stránky :pageName',
    'pages_delete_draft' => 'Odstranit koncept stránky',
    'pages_delete_success' => 'Stránka odstraněna',
    'pages_delete_draft_success' => 'Koncept stránky odstraněn',
    'pages_delete_confirm' => 'Opravdu chcete odstranit tuto stránku?',
    'pages_delete_draft_confirm' => 'Opravdu chcete odstranit tento koncept stránky?',
    'pages_editing_named' => 'Úpravy stránky :pageName',
    'pages_edit_draft_options' => 'Možnosti konceptu',
    'pages_edit_save_draft' => 'Uložit koncept',
    'pages_edit_draft' => 'Upravit koncept stránky',
    'pages_editing_draft' => 'Úprava konceptu',
    'pages_editing_page' => 'Úpravy stránky',
    'pages_edit_draft_save_at' => 'Koncept uložen v ',
    'pages_edit_delete_draft' => 'Odstranit koncept',
    'pages_edit_discard_draft' => 'Zahodit koncept',
    'pages_edit_set_changelog' => 'Nastavit protokol změn',
    'pages_edit_enter_changelog_desc' => 'Zadejte stručný popis změn, které jste provedli',
    'pages_edit_enter_changelog' => 'Zadejte protokol změn',
    'pages_save' => 'Uložit stránku',
    'pages_title' => 'Nadpis stránky',
    'pages_name' => 'Název stránky',
    'pages_md_editor' => 'Editor',
    'pages_md_preview' => 'Náhled',
    'pages_md_insert_image' => 'Vložit obrázek',
    'pages_md_insert_link' => 'Vložit odkaz na entitu',
    'pages_md_insert_drawing' => 'Vložit kresbu',
    'pages_not_in_chapter' => 'Stránka není v kapitole',
    'pages_move' => 'Přesunout stránku',
    'pages_move_success' => 'Stránka přesunuta do ":parentName"',
    'pages_copy' => 'Kopírovat stránku',
    'pages_copy_desination' => 'Cíl kopírování',
    'pages_copy_success' => 'Stránka byla zkopírována',
    'pages_permissions' => 'Oprávnění stránky',
    'pages_permissions_success' => 'Oprávnění stránky byla aktualizována',
    'pages_revision' => 'Revize',
    'pages_revisions' => 'Revize stránky',
    'pages_revisions_named' => 'Revize stránky pro :pageName',
    'pages_revision_named' => 'Revize stránky pro :pageName',
    'pages_revision_restored_from' => 'Obnoveno z #:id; :summary',
    'pages_revisions_created_by' => 'Vytvořeno uživatelem',
    'pages_revisions_date' => 'Datum revize',
    'pages_revisions_number' => 'Č. ',
    'pages_revisions_numbered' => 'Revize č. :id',
    'pages_revisions_numbered_changes' => 'Změny revize č. :id',
    'pages_revisions_changelog' => 'Protokol změn',
    'pages_revisions_changes' => 'Změny',
    'pages_revisions_current' => 'Aktuální verze',
    'pages_revisions_preview' => 'Náhled',
    'pages_revisions_restore' => 'Obnovit',
    'pages_revisions_none' => 'Tato stránka nemá žádné revize',
    'pages_copy_link' => 'Kopírovat odkaz',
    'pages_edit_content_link' => 'Upravit obsah',
    'pages_permissions_active' => 'Oprávnění stránky byla aktivována',
    'pages_initial_revision' => 'První vydání',
    'pages_initial_name' => 'Nová stránka',
    'pages_editing_draft_notification' => 'Právě upravujete koncept, který byl uložen před :timeDiff.',
    'pages_draft_edited_notification' => 'Tato stránka se od té doby změnila. Je doporučeno aktuální koncept zahodit.',
    'pages_draft_page_changed_since_creation' => 'Tato stránka byla aktualizována od vytvoření tohoto konceptu. Doporučuje se zrušit tento koncept nebo se postarat o to, abyste si nepřepsali žádné již zadané změny.',
    'pages_draft_edit_active' => [
        'start_a' => 'Uživatelé začali upravovat tuto stránku (celkem :count)',
        'start_b' => ':userName začal/a upravovat tuto stránku',
        'time_a' => 'od doby, kdy byla tato stránky naposledy aktualizována',
        'time_b' => 'v posledních minutách (:minCount min.)',
        'message' => ':start :time. Dávejte pozor abyste nepřepsali změny ostatním!',
    ],
    'pages_draft_discarded' => 'Koncept zahozen. Editor nyní obsahuje aktuální verzi stránky.',
    'pages_specific' => 'Konkrétní stránka',
    'pages_is_template' => 'Šablona stránky',

    // Editor Sidebar
    'page_tags' => 'Štítky stránky',
    'chapter_tags' => 'Štítky kapitoly',
    'recipe_tags' => 'Štítky knihy',
    'menu_tags' => 'Štítky knihovny',
    'tag' => 'Štítek',
    'tags' =>  'Štítky',
    'tag_name' =>  'Název štítku',
    'tag_value' => 'Hodnota štítku (volitelné)',
    'tags_explain' => "Přidejte si štítky pro lepší kategorizaci knih. \n Štítky mohou nést i hodnotu pro detailnější klasifikaci.",
    'tags_add' => 'Přidat další štítek',
    'tags_remove' => 'Odstranit tento štítek',
    'tags_usages' => 'Total tag usages',
    'tags_assigned_pages' => 'Assigned to Pages',
    'tags_assigned_chapters' => 'Assigned to Chapters',
    'tags_assigned_recipes' => 'Assigned to Recipes',
    'tags_assigned_menus' => 'Assigned to Menus',
    'tags_x_unique_values' => ':count unique values',
    'tags_all_values' => 'All values',
    'tags_view_tags' => 'View Tags',
    'tags_view_existing_tags' => 'View existing tags',
    'tags_list_empty_hint' => 'Tags can be assigned via the page editor sidebar or while editing the details of a recipe, chapter or menu.',
    'attachments' => 'Přílohy',
    'attachments_explain' => 'Nahrajte soubory nebo připojte odkazy, které se zobrazí na stránce. Budou k nalezení v postranní liště.',
    'attachments_explain_instant_save' => 'Změny zde provedené se okamžitě ukládají.',
    'attachments_items' => 'Připojené položky',
    'attachments_upload' => 'Nahrát soubor',
    'attachments_link' => 'Připojit odkaz',
    'attachments_set_link' => 'Nastavit odkaz',
    'attachments_delete' => 'Jste si jisti, že chcete odstranit tuto přílohu?',
    'attachments_dropzone' => 'Přetáhněte sem soubory myší nebo sem klikněte pro vybrání souboru',
    'attachments_no_files' => 'Žádné soubory nebyly nahrány',
    'attachments_explain_link' => 'Můžete pouze připojit odkaz pokud nechcete nahrávat soubor přímo. Může to být odkaz na jinou stránku nebo na soubor v cloudu.',
    'attachments_link_name' => 'Název odkazu',
    'attachment_link' => 'Odkaz na přílohu',
    'attachments_link_url' => 'Odkaz na soubor',
    'attachments_link_url_hint' => 'URL stránky nebo souboru',
    'attach' => 'Připojit',
    'attachments_insert_link' => 'Přidat odkaz na přílohu do stránky',
    'attachments_edit_file' => 'Upravit soubor',
    'attachments_edit_file_name' => 'Název souboru',
    'attachments_edit_drop_upload' => 'Přetáhněte sem soubor myší nebo klikněte pro nahrání nového souboru a následné přepsání starého',
    'attachments_order_updated' => 'Pořadí příloh aktualizováno',
    'attachments_updated_success' => 'Podrobnosti příloh aktualizovány',
    'attachments_deleted' => 'Příloha byla odstraněna',
    'attachments_file_uploaded' => 'Soubor byl nahrán',
    'attachments_file_updated' => 'Soubor byl aktualizován',
    'attachments_link_attached' => 'Odkaz byl přiložen ke stránce',
    'templates' => 'Šablony',
    'templates_set_as_template' => 'Tato stránka je šablona',
    'templates_explain_set_as_template' => 'Tuto stránku můžete nastavit jako šablonu, aby byl její obsah využit při vytváření dalších stránek. Ostatní uživatelé budou moci použít tuto šablonu, pokud mají oprávnění k zobrazení této stránky.',
    'templates_replace_content' => 'Nahradit obsah stránky',
    'templates_append_content' => 'Připojit za obsah stránky',
    'templates_prepend_content' => 'Připojit před obsah stránky',

    // Profile View
    'profile_user_for_x' => 'Uživatelem již :time',
    'profile_created_content' => 'Vytvořený obsah',
    'profile_not_created_pages' => ':userName nevytvořil/a žádné stránky',
    'profile_not_created_chapters' => ':userName nevytvořil/a žádné kapitoly',
    'profile_not_created_recipes' => ':userName nevytvořil/a žádné knihy',
    'profile_not_created_menus' => ':userName nevytvořil/a žádné knihovny',

    // Comments
    'comment' => 'Komentář',
    'comments' => 'Komentáře',
    'comment_add' => 'Přidat komentář',
    'comment_placeholder' => 'Zde zadejte komentář',
    'comment_count' => '{0} Bez komentářů|{1} 1 komentář|[2,4] :count komentáře|[5,*] :count komentářů',
    'comment_save' => 'Uložit komentář',
    'comment_saving' => 'Ukládání komentáře...',
    'comment_deleting' => 'Mazání komentáře...',
    'comment_new' => 'Nový komentář',
    'comment_created' => 'komentováno :createDiff',
    'comment_updated' => 'Aktualizováno :updateDiff uživatelem :username',
    'comment_deleted_success' => 'Komentář odstraněn',
    'comment_created_success' => 'Komentář přidán',
    'comment_updated_success' => 'Komentář aktualizován',
    'comment_delete_confirm' => 'Opravdu chcete odstranit tento komentář?',
    'comment_in_reply_to' => 'Odpověď na :commentId',

    // Revision
    'revision_delete_confirm' => 'Opravdu chcete odstranit tuto revizi?',
    'revision_restore_confirm' => 'Jste si jisti, že chcete obnovit tuto revizi? Aktuální obsah stránky bude nahrazen.',
    'revision_delete_success' => 'Revize odstraněna',
    'revision_cannot_delete_latest' => 'Nelze odstranit poslední revizi.',
];
