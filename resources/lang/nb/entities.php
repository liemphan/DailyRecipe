<?php
/**
 * Text used for 'Entities' (Document Structure Elements) such as
 * Books, Menus, Chapters & Pages
 */
return [

    // Shared
    'recently_created' => 'Nylig opprettet',
    'recently_created_pages' => 'Nylig opprettede sider',
    'recently_updated_pages' => 'Nylig oppdaterte sider',
    'recently_created_chapters' => 'Nylig opprettede kapitler',
    'recently_created_books' => 'Nylig opprettede bøker',
    'recently_created_menus' => 'Nylig opprettede bokhyller',
    'recently_update' => 'Nylig oppdatert',
    'recently_viewed' => 'Nylig vist',
    'recent_activity' => 'Nylig aktivitet',
    'create_now' => 'Opprett en nå',
    'revisions' => 'Revisjoner',
    'meta_revision' => 'Revisjon #:revisionCount',
    'meta_created' => 'Opprettet :timeLength',
    'meta_created_name' => 'Opprettet :timeLength av :user',
    'meta_updated' => 'Oppdatert :timeLength',
    'meta_updated_name' => 'Oppdatert :timeLength av :user',
    'meta_owned_name' => 'Eies av :user',
    'entity_select' => 'Velg entitet',
    'images' => 'Bilder',
    'my_recent_drafts' => 'Mine nylige utkast',
    'my_recently_viewed' => 'Mine nylige visninger',
    'my_most_viewed_favourites' => 'Mine mest viste favoritter',
    'my_favourites' => 'Mine favoritter',
    'no_pages_viewed' => 'Du har ikke sett på noen sider',
    'no_pages_recently_created' => 'Ingen sider har nylig blitt opprettet',
    'no_pages_recently_updated' => 'Ingen sider har nylig blitt oppdatert',
    'export' => 'Eksporter',
    'export_html' => 'Nettside med alt',
    'export_pdf' => 'PDF Fil',
    'export_text' => 'Tekstfil',
    'export_md' => 'Markdownfil',

    // Permissions and restrictions
    'permissions' => 'Tilganger',
    'permissions_intro' => 'Når disse er tillatt, vil disse tillatelsene ha prioritet over alle angitte rolletillatelser.',
    'permissions_enable' => 'Aktiver egendefinerte tillatelser',
    'permissions_save' => 'Lagre tillatelser',
    'permissions_owner' => 'Eier',

    // Search
    'search_results' => 'Søkeresultater',
    'search_total_results_found' => ':count resultater funnet|:count totalt',
    'search_clear' => 'Nullstill søk',
    'search_no_pages' => 'Ingen sider passer med søket',
    'search_for_term' => 'Søk etter :term',
    'search_more' => 'Flere resultater',
    'search_advanced' => 'Avansert søk',
    'search_terms' => 'Søkeord',
    'search_content_type' => 'Innholdstype',
    'search_exact_matches' => 'Eksakte ord',
    'search_tags' => 'Søk på merker',
    'search_options' => 'ALternativer',
    'search_viewed_by_me' => 'Sett av meg',
    'search_not_viewed_by_me' => 'Ikke sett av meg',
    'search_permissions_set' => 'Tilganger er angitt',
    'search_created_by_me' => 'Opprettet av meg',
    'search_updated_by_me' => 'Oppdatert av meg',
    'search_owned_by_me' => 'Eid av meg',
    'search_date_options' => 'Datoalternativer',
    'search_updated_before' => 'Oppdatert før',
    'search_updated_after' => 'Oppdatert etter',
    'search_created_before' => 'Opprettet før',
    'search_created_after' => 'Opprettet etter',
    'search_set_date' => 'Angi dato',
    'search_update' => 'Oppdater søk',

    // Menus
    'menu' => 'Hylle',
    'menus' => 'Hyller',
    'x_menus' => ':count hylle|:count hyller',
    'menus_long' => 'Bokhyller',
    'menus_empty' => 'Ingen bokhyller er opprettet',
    'menus_create' => 'Opprett ny bokhylle',
    'menus_popular' => 'Populære bokhyller',
    'menus_new' => 'Nye bokhyller',
    'menus_new_action' => 'Ny bokhylle',
    'menus_popular_empty' => 'De mest populære bokhyllene blir vist her.',
    'menus_new_empty' => 'Nylig opprettede bokhyller vises her.',
    'menus_save' => 'Lagre hylle',
    'menus_books' => 'Bøker på denne hyllen',
    'menus_add_books' => 'Legg til bøker på denne hyllen',
    'menus_drag_books' => 'Dra bøker hit for å stable dem i denne hylla',
    'menus_empty_contents' => 'INgen bøker er stablet i denne hylla',
    'menus_edit_and_assign' => 'Endre hylla for å legge til bøker',
    'menus_edit_named' => 'Endre hyllen :name',
    'menus_edit' => 'Endre bokhylle',
    'menus_delete' => 'Fjern bokhylle',
    'menus_delete_named' => 'Fjern bokhyllen :name',
    'menus_delete_explain' => "Dette vil fjerne bokhyllen ':name'. Bøkene vil ikke fjernes fra systemet.",
    'menus_delete_confirmation' => 'Er du helt sikker på at du vil skru ned hylla?',
    'menus_permissions' => 'Tilganger til hylla',
    'menus_permissions_updated' => 'Hyllas tilganger er oppdatert',
    'menus_permissions_active' => 'Hyllas tilganger er aktive',
    'menus_permissions_cascade_warning' => 'Permissions on recipemenus do not automatically cascade to contained recipes. This is because a book can exist on multiple menus. Permissions can however be copied down to child recipes using the option found below.',
    'menus_copy_permissions_to_books' => 'Kopier tilganger til bøkene på hylla',
    'menus_copy_permissions' => 'Kopier tilganger',
    'menus_copy_permissions_explain' => 'Dette vil angi gjeldende tillatelsesinnstillinger for denne bokhyllen på alle bøkene som finnes på den. Før du aktiverer, må du forsikre deg om at endringer i tillatelsene til denne bokhyllen er lagret.',
    'menus_copy_permission_success' => 'Tilgangene ble overført til :count bøker',

    // Books
    'book' => 'Bok',
    'recipes' => 'Bøker',
    'x_books' => ':count bok|:count bøker',
    'books_empty' => 'Ingen bøker er skrevet',
    'books_popular' => 'Populære bøker',
    'books_recent' => 'Nylige bøker',
    'books_new' => 'Nye bøker',
    'books_new_action' => 'Ny bok',
    'books_popular_empty' => 'De mest populære bøkene',
    'books_new_empty' => 'Siste utgivelser vises her.',
    'books_create' => 'Skriv ny bok',
    'books_delete' => 'Brenn bok',
    'books_delete_named' => 'Brenn boken :bookName',
    'books_delete_explain' => 'Dette vil brenne boken «:bookName». Alle sider i boken vil fordufte for godt.',
    'books_delete_confirmation' => 'Er du sikker på at du vil brenne boken?',
    'books_edit' => 'Endre bok',
    'books_edit_named' => 'Endre boken :bookName',
    'books_form_book_name' => 'Boktittel',
    'books_save' => 'Lagre bok',
    'books_permissions' => 'Boktilganger',
    'books_permissions_updated' => 'Boktilganger oppdatert',
    'books_empty_contents' => 'Ingen sider eller kapitler finnes i denne boken.',
    'books_empty_create_page' => 'Skriv en ny side',
    'books_empty_sort_current_book' => 'Sorter innholdet i boken',
    'books_empty_add_chapter' => 'Start på nytt kapittel',
    'books_permissions_active' => 'Boktilganger er aktive',
    'books_search_this' => 'Søk i boken',
    'books_navigation' => 'Boknavigasjon',
    'books_sort' => 'Sorter bokinnhold',
    'books_sort_named' => 'Sorter boken :bookName',
    'books_sort_name' => 'Sorter på navn',
    'books_sort_created' => 'Sorter på opprettet dato',
    'books_sort_updated' => 'Sorter på oppdatert dato',
    'books_sort_chapters_first' => 'Kapitler først',
    'books_sort_chapters_last' => 'Kapitler sist',
    'books_sort_show_other' => 'Vis andre bøker',
    'books_sort_save' => 'Lagre sortering',

    // Chapters
    'chapter' => 'Kapittel',
    'chapters' => 'Kapitler',
    'x_chapters' => ':count Kapittel|:count Kapitler',
    'chapters_popular' => 'Populære kapittler',
    'chapters_new' => 'Nytt kapittel',
    'chapters_create' => 'Skriv nytt kapittel',
    'chapters_delete' => 'Riv ut kapittel',
    'chapters_delete_named' => 'Riv ut kapittelet :chapterName',
    'chapters_delete_explain' => 'Du ønsker å rive ut kapittelet «:chapterName». Alle sidene vil bli flyttet ut av kapittelet og vil ligge direkte i boka.',
    'chapters_delete_confirm' => 'Er du sikker på at du vil rive ut dette kapittelet?',
    'chapters_edit' => 'Endre kapittel',
    'chapters_edit_named' => 'Endre kapittelet :chapterName',
    'chapters_save' => 'Lagre kapittel',
    'chapters_move' => 'Flytt kapittel',
    'chapters_move_named' => 'Flytt kapittelet :chapterName',
    'chapter_move_success' => 'Kapittelet ble flyttet til :bookName',
    'chapters_permissions' => 'Kapitteltilganger',
    'chapters_empty' => 'Det finnes ingen sider i dette kapittelet.',
    'chapters_permissions_active' => 'Kapitteltilganger er aktivert',
    'chapters_permissions_success' => 'Kapitteltilgager er oppdatert',
    'chapters_search_this' => 'Søk i dette kapittelet',

    // Pages
    'page' => 'Side',
    'pages' => 'Sider',
    'x_pages' => ':count side|:count sider',
    'pages_popular' => 'Populære sider',
    'pages_new' => 'Ny side',
    'pages_attachments' => 'Vedlegg',
    'pages_navigation' => 'Sidenavigasjon',
    'pages_delete' => 'Riv ut side',
    'pages_delete_named' => 'Riv ut siden :pageName',
    'pages_delete_draft_named' => 'Kast sideutkast :pageName',
    'pages_delete_draft' => 'Kast sideutkast',
    'pages_delete_success' => 'Siden er revet ut',
    'pages_delete_draft_success' => 'Sideutkast er kastet',
    'pages_delete_confirm' => 'Er du sikker på at du vil rive ut siden?',
    'pages_delete_draft_confirm' => 'Er du sikker på at du vil forkaste utkastet?',
    'pages_editing_named' => 'Endrer :pageName',
    'pages_edit_draft_options' => 'Utkastsalternativer',
    'pages_edit_save_draft' => 'Lagre utkast',
    'pages_edit_draft' => 'Endre utkast',
    'pages_editing_draft' => 'Redigerer utkast',
    'pages_editing_page' => 'Redigerer side',
    'pages_edit_draft_save_at' => 'Ukast lagret under ',
    'pages_edit_delete_draft' => 'Forkast utkast',
    'pages_edit_discard_draft' => 'Gi opp utkast',
    'pages_edit_set_changelog' => 'Angi endringslogg',
    'pages_edit_enter_changelog_desc' => 'Gi en kort beskrivelse av endringene dine',
    'pages_edit_enter_changelog' => 'Se endringslogg',
    'pages_save' => 'Lagre side',
    'pages_title' => 'Sidetittel',
    'pages_name' => 'Sidenavn',
    'pages_md_editor' => 'Tekstbehandler',
    'pages_md_preview' => 'Forhåndsvisning',
    'pages_md_insert_image' => 'Lim inn bilde',
    'pages_md_insert_link' => 'Lim in lenke',
    'pages_md_insert_drawing' => 'Lim inn tegning',
    'pages_not_in_chapter' => 'Siden tilhører ingen kapittel',
    'pages_move' => 'Flytt side',
    'pages_move_success' => 'Siden ble flyttet til ":parentName"',
    'pages_copy' => 'Kopier side',
    'pages_copy_desination' => 'Destinasjon',
    'pages_copy_success' => 'Siden ble flyttet',
    'pages_permissions' => 'Sidetilganger',
    'pages_permissions_success' => 'Sidens tilganger ble endret',
    'pages_revision' => 'Revisjon',
    'pages_revisions' => 'Sidens revisjoner',
    'pages_revisions_named' => 'Revisjoner for :pageName',
    'pages_revision_named' => 'Revisjoner for :pageName',
    'pages_revision_restored_from' => 'Gjenopprettet fra #:id; :summary',
    'pages_revisions_created_by' => 'Skrevet av',
    'pages_revisions_date' => 'Revideringsdato',
    'pages_revisions_number' => '#',
    'pages_revisions_numbered' => 'Revisjon #:id',
    'pages_revisions_numbered_changes' => 'Endringer på revisjon #:id',
    'pages_revisions_changelog' => 'Endringslogg',
    'pages_revisions_changes' => 'Endringer',
    'pages_revisions_current' => 'Siste versjon',
    'pages_revisions_preview' => 'Forhåndsvisning',
    'pages_revisions_restore' => 'Gjenopprett',
    'pages_revisions_none' => 'Denne siden har ingen revisjoner',
    'pages_copy_link' => 'Kopier lenke',
    'pages_edit_content_link' => 'Endre innhold',
    'pages_permissions_active' => 'Sidetilganger er aktive',
    'pages_initial_revision' => 'Første publisering',
    'pages_initial_name' => 'Ny side',
    'pages_editing_draft_notification' => 'Du skriver på et utkast som sist ble lagret :timeDiff.',
    'pages_draft_edited_notification' => 'Siden har blitt endret siden du startet. Det anbefales at du forkaster dine endringer.',
    'pages_draft_page_changed_since_creation' => 'This page has been updated since this draft was created. It is recommended that you discard this draft or take care not to overwrite any page changes.',
    'pages_draft_edit_active' => [
        'start_a' => ':count forfattere har begynt å endre denne siden.',
        'start_b' => ':userName skriver på siden for øyeblikket',
        'time_a' => 'siden sist siden ble oppdatert',
        'time_b' => 'i løpet av de siste :minCount minuttene',
        'message' => ':start :time. Prøv å ikke overskriv hverandres endringer!',
    ],
    'pages_draft_discarded' => 'Forkastet, viser nå siste endringer fra siden slik den er lagret.',
    'pages_specific' => 'Bestemt side',
    'pages_is_template' => 'Sidemal',

    // Editor Sidebar
    'page_tags' => 'Sidemerker',
    'chapter_tags' => 'Kapittelmerker',
    'book_tags' => 'Bokmerker',
    'menu_tags' => 'Hyllemerker',
    'tag' => 'Merke',
    'tags' =>  'Merker',
    'tag_name' =>  'Merketittel',
    'tag_value' => 'Merkeverdi (Valgfritt)',
    'tags_explain' => "Legg til merker for å kategorisere innholdet ditt. \n Du kan legge til merkeverdier for å beskrive dem ytterligere.",
    'tags_add' => 'Legg til flere merker',
    'tags_remove' => 'Fjern merke',
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
    'attachments' => 'Vedlegg',
    'attachments_explain' => 'Last opp vedlegg eller legg til lenker for å berike innholdet. Disse vil vises i sidestolpen på siden.',
    'attachments_explain_instant_save' => 'Endringer her blir lagret med en gang.',
    'attachments_items' => 'Vedlegg',
    'attachments_upload' => 'Last opp vedlegg',
    'attachments_link' => 'Fest lenke',
    'attachments_set_link' => 'Angi lenke',
    'attachments_delete' => 'Er du sikker på at du vil fjerne vedlegget?',
    'attachments_dropzone' => 'Dra og slipp eller trykk her for å feste vedlegg',
    'attachments_no_files' => 'Ingen vedlegg er lastet opp',
    'attachments_explain_link' => 'Du kan feste lenker til denne. Det kan være henvisning til andre sider, bøker etc. eller lenker fra nettet.',
    'attachments_link_name' => 'Lenkenavn',
    'attachment_link' => 'Vedleggslenke',
    'attachments_link_url' => 'Lenke til vedlegg',
    'attachments_link_url_hint' => 'Adresse til lenke eller vedlegg',
    'attach' => 'Fest',
    'attachments_insert_link' => 'Fest vedleggslenke',
    'attachments_edit_file' => 'Endre vedlegg',
    'attachments_edit_file_name' => 'Vedleggsnavn',
    'attachments_edit_drop_upload' => 'Dra og slipp eller trykk her for å oppdatere eller overskrive',
    'attachments_order_updated' => 'Vedleggssortering endret',
    'attachments_updated_success' => 'Vedleggsdetaljer endret',
    'attachments_deleted' => 'Vedlegg fjernet',
    'attachments_file_uploaded' => 'Vedlegg ble lastet opp',
    'attachments_file_updated' => 'Vedlegget ble oppdatert',
    'attachments_link_attached' => 'Lenken ble festet til siden',
    'templates' => 'Maler',
    'templates_set_as_template' => 'Siden er en mal',
    'templates_explain_set_as_template' => 'Du kan angi denne siden som en mal slik at innholdet kan brukes når du oppretter andre sider. Andre brukere vil kunne bruke denne malen hvis de har visningstillatelser for denne siden.',
    'templates_replace_content' => 'Bytt sideinnhold',
    'templates_append_content' => 'Legg til neders på siden',
    'templates_prepend_content' => 'Legg til øverst på siden',

    // Profile View
    'profile_user_for_x' => 'Medlem i :time',
    'profile_created_content' => 'Har skrevet',
    'profile_not_created_pages' => ':userName har ikke forfattet noen sider',
    'profile_not_created_chapters' => ':userName har ikke opprettet noen kapitler',
    'profile_not_created_books' => ':userName har ikke laget noen bøker',
    'profile_not_created_menus' => ':userName har ikke hengt opp noen hyller',

    // Comments
    'comment' => 'Kommentar',
    'comments' => 'Kommentarer',
    'comment_add' => 'Skriv kommentar',
    'comment_placeholder' => 'Skriv en kommentar her',
    'comment_count' => '{0} Ingen kommentarer|{1} 1 kommentar|[2,*] :count kommentarer',
    'comment_save' => 'Publiser kommentar',
    'comment_saving' => 'Publiserer ...',
    'comment_deleting' => 'Fjerner...',
    'comment_new' => 'Ny kommentar',
    'comment_created' => 'kommenterte :createDiff',
    'comment_updated' => 'Oppdatert :updateDiff av :username',
    'comment_deleted_success' => 'Kommentar fjernet',
    'comment_created_success' => 'Kommentar skrevet',
    'comment_updated_success' => 'Kommentar endret',
    'comment_delete_confirm' => 'Er du sikker på at du vil fjerne kommentaren?',
    'comment_in_reply_to' => 'Som svar til :commentId',

    // Revision
    'revision_delete_confirm' => 'Vil du slette revisjonen?',
    'revision_restore_confirm' => 'Vil du gjenopprette revisjonen? Innholdet på siden vil bli overskrevet med denne revisjonen.',
    'revision_delete_success' => 'Revisjonen ble slettet',
    'revision_cannot_delete_latest' => 'CKan ikke slette siste revisjon.',
];
