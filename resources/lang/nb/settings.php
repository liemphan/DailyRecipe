<?php
/**
 * Settings text strings
 * Contains all text strings used in the general settings sections of DailyRecipe
 * including users and roles.
 */
return [

    // Common Messages
    'settings' => 'Innstillinger',
    'settings_save' => 'Lagre innstillinger',
    'settings_save_success' => 'Innstillinger lagret',

    // App Settings
    'app_customization' => 'Tilpassing',
    'app_features_security' => 'Funksjoner og sikkerhet',
    'app_name' => 'Applikasjonsnavn',
    'app_name_desc' => 'Dette navnet vises i overskriften og i alle e-postmeldinger som sendes av systemet.',
    'app_name_header' => 'Vis navn i topptekst',
    'app_public_access' => 'Offentlig tilgang',
    'app_public_access_desc' => 'Hvis du aktiverer dette alternativet, kan besøkende, som ikke er logget på, få tilgang til innhold i din DailyRecipe-forekomst.',
    'app_public_access_desc_guest' => 'Tilgang for offentlige besøkende kan kontrolleres gjennom "Gjest" -brukeren.',
    'app_public_access_toggle' => 'Tillat offentlig tilgang',
    'app_public_viewing' => 'Tillat offentlig visning?',
    'app_secure_images' => 'Høyere sikkerhet på bildeopplastinger',
    'app_secure_images_toggle' => 'Enable høyere sikkerhet på bildeopplastinger',
    'app_secure_images_desc' => 'Av ytelsesgrunner er alle bilder offentlige. Dette alternativet legger til en tilfeldig streng som er vanskelig å gjette foran bildets nettadresser. Forsikre deg om at katalogindekser ikke er aktivert for å forhindre enkel tilgang.',
    'app_editor' => 'Tekstbehandler',
    'app_editor_desc' => 'Velg hvilken tekstbehandler som skal brukes av alle brukere til å redigere sider.',
    'app_custom_html' => 'Tilpasset HTML-hodeinnhold',
    'app_custom_html_desc' => 'Alt innhold som legges til her, blir satt inn i bunnen av <head> -delen på hver side. Dette er praktisk for å overstyre stiler eller legge til analysekode.',
    'app_custom_html_disabled_notice' => 'Tilpasset HTML-hodeinnhold er deaktivert på denne innstillingssiden for å sikre at eventuelle endringer ødelegger noe, kan tilbakestilles.',
    'app_logo' => 'Applikasjonslogo',
    'app_logo_desc' => 'Dette bildet skal være 43 px høyt. <br> Store bilder blir nedskalert.',
    'app_primary_color' => 'Applikasjonens primærfarge',
    'app_primary_color_desc' => 'Angir primærfargen for applikasjonen inkludert banner, knapper og lenker.',
    'app_homepage' => 'Applikasjonens hjemmeside',
    'app_homepage_desc' => 'Velg en visning som skal vises på hjemmesiden i stedet for standardvisningen. Sidetillatelser ignoreres for utvalgte sider.',
    'app_homepage_select' => 'Velg en side',
    'app_footer_links' => 'Fotlenker',
    'app_footer_links_desc' => 'Legg til fotlenker i sidens fotområde. Disse vil vises nederst på de fleste sider, inkludert sider som ikke krever innlogging. Du kan bruke «trans::<key>» etiketter for system-definerte oversettelser. For eksempel: Bruk «trans::common.privacy_policy» for å vise teksten «Personvernregler» og «trans::common.terms_of_service» for å vise teksten «Bruksvilkår».',
    'app_footer_links_label' => 'Lenketekst',
    'app_footer_links_url' => 'Lenke',
    'app_footer_links_add' => 'Legg til fotlenke',
    'app_disable_comments' => 'Deaktiver kommentarer',
    'app_disable_comments_toggle' => 'Deaktiver kommentarer',
    'app_disable_comments_desc' => 'Deaktiver kommentarer på tvers av alle sidene i applikasjonen. <br> Eksisterende kommentarer vises ikke.',

    // Color settings
    'content_colors' => 'Innholdsfarger',
    'content_colors_desc' => 'Angir farger for alle elementene i sideorganisasjonshierarkiet. Det anbefales å lese farger med en lignende lysstyrke som standardfargene for lesbarhet.',
    'bookshelf_color' => 'Hyllefarge',
    'book_color' => 'Bokfarge',
    'chapter_color' => 'Kapittelfarge',
    'page_color' => 'Sidefarge',
    'page_draft_color' => 'Sideutkastsfarge',

    // Registration Settings
    'reg_settings' => 'Registrering',
    'reg_enable' => 'Tillat registrering',
    'reg_enable_toggle' => 'Tillat registrering',
    'reg_enable_desc' => 'Når registrering er aktivert vil brukeren kunne registrere seg som applikasjonsbruker. Ved registrering får de en standard brukerrolle.',
    'reg_default_role' => 'Standard brukerrolle etter registrering',
    'reg_enable_external_warning' => 'Alternativet ovenfor ignoreres mens ekstern LDAP- eller SAML-autentisering er aktiv. Brukerkontoer for ikke-eksisterende medlemmer blir automatisk opprettet hvis autentisering mot det eksterne systemet i bruk lykkes.',
    'reg_email_confirmation' => 'E-postbekreftelse',
    'reg_email_confirmation_toggle' => 'Krev e-postbekreftelse',
    'reg_confirm_email_desc' => 'Hvis domenebegrensning brukes, vil e-postbekreftelse være nødvendig, og dette alternativet vil bli ignorert.',
    'reg_confirm_restrict_domain' => 'Domenebegrensning',
    'reg_confirm_restrict_domain_desc' => 'Skriv inn en kommaseparert liste over e-postdomener du vil begrense registreringen til. Brukerne vil bli sendt en e-post for å bekrefte adressen deres før de får lov til å kommunisere med applikasjonen. <br> Vær oppmerksom på at brukere vil kunne endre e-postadressene sine etter vellykket registrering.',
    'reg_confirm_restrict_domain_placeholder' => 'Ingen begrensninger er satt',

    // Maintenance settings
    'maint' => 'Vedlikehold',
    'maint_image_cleanup' => 'Bildeopprydding',
    'maint_image_cleanup_desc' => 'Skanner side og revisjonsinnhold for å sjekke hvilke bilder og tegninger som for øyeblikket er i bruk, og hvilke bilder som er overflødige. Forsikre deg om at du lager en full database og sikkerhetskopiering av bilder før du kjører denne.',
    'maint_delete_images_only_in_revisions' => 'Slett også bilder som bare finnes i game siderevisjoner',
    'maint_image_cleanup_run' => 'Kjør opprydding',
    'maint_image_cleanup_warning' => ':count potensielt ubrukte bilder ble funnet. Er du sikker på at du vil slette disse bildene?',
    'maint_image_cleanup_success' => ':count potensielt ubrukte bilder funnet og slettet!',
    'maint_image_cleanup_nothing_found' => 'Ingen ubrukte bilder funnet, ingenting slettet!',
    'maint_send_test_email' => 'Send en test-e-post',
    'maint_send_test_email_desc' => 'Dette sender en test-e-post til din e-postadresse som er angitt i profilen din.',
    'maint_send_test_email_run' => 'Send en test-e-post',
    'maint_send_test_email_success' => 'Send en test-e-post til :address',
    'maint_send_test_email_mail_subject' => 'Test-e-post',
    'maint_send_test_email_mail_greeting' => 'E-postsending ser ut til å fungere!',
    'maint_send_test_email_mail_text' => 'Gratulerer! Da du mottok dette e-postvarselet, ser det ut til at e-postinnstillingene dine er konfigurert riktig.',
    'maint_recycle_bin_desc' => 'Slettede hyller, bøker, kapitler og sider kastes i papirkurven så de kan bli gjenopprettet eller slettet permanent. Eldre utgaver i papirkurven kan slettes automatisk etter en stund, avhengig av systemkonfigurasjonen.',
    'maint_recycle_bin_open' => 'Åpne papirkurven',

    // Recycle Bin
    'recycle_bin' => 'Papirkurven',
    'recycle_bin_desc' => 'Her kan du gjenopprette ting du har kastet i papirkurven eller velge å slette dem permanent fra systemet. Denne listen er ikke filtrert i motsetning til lignende lister i systemet hvor tilgangskontroll overholdes.',
    'recycle_bin_deleted_item' => 'Kastet element',
    'recycle_bin_deleted_parent' => 'Overordnet',
    'recycle_bin_deleted_by' => 'Kastet av',
    'recycle_bin_deleted_at' => 'Kastet den',
    'recycle_bin_permanently_delete' => 'Slett permanent',
    'recycle_bin_restore' => 'Gjenopprett',
    'recycle_bin_contents_empty' => 'Papirkurven er for øyeblikket tom',
    'recycle_bin_empty' => 'Tøm papirkurven',
    'recycle_bin_empty_confirm' => 'Dette vil slette alle elementene i papirkurven permanent. Dette inkluderer innhold i hvert element. Er du sikker på at du vil tømme papirkurven?',
    'recycle_bin_destroy_confirm' => 'Denne handlingen vil permanent slette dette elementet og alle dets underelementer fra systemet, som beskrevet nedenfor. Du vil ikke kunne gjenopprette dette innholdet med mindre du har en tidligere sikkerhetskopi av databasen. Er du sikker på at du vil fortsette?',
    'recycle_bin_destroy_list' => 'Elementer som skal slettes',
    'recycle_bin_restore_list' => 'Elementer som skal gjenopprettes',
    'recycle_bin_restore_confirm' => 'Denne handlingen vil hente opp elementet fra papirkurven, inkludert underliggende innhold, til sin opprinnelige sted. Om den opprinnelige plassen har blitt slettet i mellomtiden og nå befinner seg i papirkurven, vil også dette bli hentet opp igjen.',
    'recycle_bin_restore_deleted_parent' => 'Det overordnede elementet var også kastet i papirkurven. Disse elementene vil forbli kastet inntil det overordnede også hentes opp igjen.',
    'recycle_bin_restore_parent' => 'Gjenopprett overodnet',
    'recycle_bin_destroy_notification' => 'Slettet :count elementer fra papirkurven.',
    'recycle_bin_restore_notification' => 'Gjenopprettet :count elementer fra papirkurven.',

    // Audit Log
    'audit' => 'Revisjonslogg',
    'audit_desc' => 'Denne revisjonsloggen viser en liste over aktiviteter som spores i systemet. Denne listen er ufiltrert i motsetning til lignende aktivitetslister i systemet der tillatelsesfiltre brukes.',
    'audit_event_filter' => 'Hendelsesfilter',
    'audit_event_filter_no_filter' => 'Ingen filter',
    'audit_deleted_item' => 'Slettet ting',
    'audit_deleted_item_name' => 'Navn: :name',
    'audit_table_user' => 'Kontoholder',
    'audit_table_event' => 'Hendelse',
    'audit_table_related' => 'Relaterte elementer eller detaljer',
    'audit_table_ip' => 'IP Address',
    'audit_table_date' => 'Aktivitetsdato',
    'audit_date_from' => 'Datoperiode fra',
    'audit_date_to' => 'Datoperiode til',

    // Role Settings
    'roles' => 'Roller',
    'role_user_roles' => 'Kontoroller',
    'role_create' => 'Opprett ny rolle',
    'role_create_success' => 'Rolle opprettet',
    'role_delete' => 'Rolle slettet',
    'role_delete_confirm' => 'Dette vil slette rollen «:roleName».',
    'role_delete_users_assigned' => 'Denne rollen har :userCount kontoer koblet opp mot seg. Velg hvilke rolle du vil flytte disse til.',
    'role_delete_no_migration' => "Ikke flytt kontoer",
    'role_delete_sure' => 'Er du sikker på at du vil slette rollen?',
    'role_delete_success' => 'Rollen ble slettet',
    'role_edit' => 'Endre rolle',
    'role_details' => 'Rolledetaljer',
    'role_name' => 'Rollenavn',
    'role_desc' => 'Kort beskrivelse av rolle',
    'role_mfa_enforced' => 'Krever flerfaktorautentisering',
    'role_external_auth_id' => 'Ekstern godkjennings-ID',
    'role_system' => 'Systemtilganger',
    'role_manage_users' => 'Behandle kontoer',
    'role_manage_roles' => 'Behandle roller og rolletilganger',
    'role_manage_entity_permissions' => 'Behandle bok-, kapittel- og sidetilganger',
    'role_manage_own_entity_permissions' => 'Behandle tilganger på egne verk',
    'role_manage_page_templates' => 'Behandle sidemaler',
    'role_access_api' => 'Systemtilgang API',
    'role_manage_settings' => 'Behandle applikasjonsinnstillinger',
    'role_export_content' => 'Export content',
    'role_asset' => 'Eiendomstillatelser',
    'roles_system_warning' => 'Vær oppmerksom på at tilgang til noen av de ovennevnte tre tillatelsene kan tillate en bruker å endre sine egne rettigheter eller rettighetene til andre i systemet. Bare tildel roller med disse tillatelsene til pålitelige brukere.',
    'role_asset_desc' => 'Disse tillatelsene kontrollerer standard tilgang til eiendelene i systemet. Tillatelser til bøker, kapitler og sider overstyrer disse tillatelsene.',
    'role_asset_admins' => 'Administratorer får automatisk tilgang til alt innhold, men disse alternativene kan vise eller skjule UI-alternativer.',
    'role_all' => 'Alle',
    'role_own' => 'Egne',
    'role_controlled_by_asset' => 'Kontrollert av eiendelen de er lastet opp til',
    'role_save' => 'Lagre rolle',
    'role_update_success' => 'Rollen ble oppdatert',
    'role_users' => 'Kontoholdere med denne rollen',
    'role_users_none' => 'Ingen kontoholdere er gitt denne rollen',

    // Users
    'users' => 'Brukere',
    'user_profile' => 'Profil',
    'users_add_new' => 'Register ny konto',
    'users_search' => 'Søk i kontoer',
    'users_latest_activity' => 'Siste aktivitet',
    'users_details' => 'Kontodetaljer',
    'users_details_desc' => 'Angi et visningsnavn og en e-postadresse for denne kontoholderen. E-postadressen vil bli brukt til å logge på applikasjonen.',
    'users_details_desc_no_email' => 'Angi et visningsnavn for denne kontoholderen slik at andre kan gjenkjenne dem.',
    'users_role' => 'Roller',
    'users_role_desc' => 'Velg hvilke roller denne kontoholderen vil bli tildelt. Hvis en kontoholderen er tildelt flere roller, vil tillatelsene fra disse rollene stable seg, og de vil motta alle evnene til de tildelte rollene.',
    'users_password' => 'Passord',
    'users_password_desc' => 'Angi et passord som brukes til å logge på applikasjonen. Dette må bestå av minst 6 tegn.',
    'users_send_invite_text' => 'Du kan velge å sende denne kontoholderen en invitasjons-e-post som lar dem angi sitt eget passord, ellers kan du selv angi passordet.',
    'users_send_invite_option' => 'Send invitasjonsmelding',
    'users_external_auth_id' => 'Ekstern godkjennings-ID',
    'users_external_auth_id_desc' => 'Dette er ID-en som brukes til å matche denne kontoholderen når de kommuniserer med det eksterne autentiseringssystemet.',
    'users_password_warning' => 'Fyll bare ut nedenfor hvis du vil endre passordet ditt.',
    'users_system_public' => 'Denne brukeren representerer alle gjester som besøker appliaksjonen din. Den kan ikke brukes til å logge på, men tildeles automatisk.',
    'users_delete' => 'Slett konto',
    'users_delete_named' => 'Slett kontoen :userName',
    'users_delete_warning' => 'Dette vil fullstendig slette denne brukeren med navnet «:userName» fra systemet.',
    'users_delete_confirm' => 'Er du sikker på at du vil slette denne kontoen?',
    'users_migrate_ownership' => 'Overfør eierskap',
    'users_migrate_ownership_desc' => 'Velg en bruker her, som du ønsker skal ta eierskap over alle elementene som er eid av denne brukeren.',
    'users_none_selected' => 'Ingen bruker valgt',
    'users_delete_success' => 'Konto slettet',
    'users_edit' => 'Rediger konto',
    'users_edit_profile' => 'Rediger profil',
    'users_edit_success' => 'Kontoen ble oppdatert',
    'users_avatar' => 'Kontobilde',
    'users_avatar_desc' => 'Velg et bilde for å representere denne kontoholderen. Dette skal være omtrent 256px kvadrat.',
    'users_preferred_language' => 'Foretrukket språk',
    'users_preferred_language_desc' => 'Dette alternativet vil endre språket som brukes til brukergrensesnittet til applikasjonen. Dette påvirker ikke noe brukeropprettet innhold.',
    'users_social_accounts' => 'Sosiale kontoer',
    'users_social_accounts_info' => 'Her kan du koble andre kontoer for raskere og enklere pålogging. Hvis du frakobler en konto her, tilbakekaller ikke dette tidligere autorisert tilgang. Tilbakekall tilgang fra profilinnstillingene dine på den tilkoblede sosiale kontoen.',
    'users_social_connect' => 'Koble til konto',
    'users_social_disconnect' => 'Koble fra konto',
    'users_social_connected' => ':socialAccount ble lagt til din konto.',
    'users_social_disconnected' => ':socialAccount ble koblet fra din konto.',
    'users_api_tokens' => 'API-nøkler',
    'users_api_tokens_none' => 'Ingen API-nøkler finnes for denne kontoen',
    'users_api_tokens_create' => 'Opprett nøkkel',
    'users_api_tokens_expires' => 'Utløper',
    'users_api_tokens_docs' => 'API-dokumentasjon',
    'users_mfa' => 'Flerfaktorautentisering',
    'users_mfa_desc' => 'Konfigurer flerfaktorautentisering som et ekstra lag med sikkerhet for din konto.',
    'users_mfa_x_methods' => ':count metode konfigurert|:count metoder konfigurert',
    'users_mfa_configure' => 'Konfigurer metoder',

    // API Tokens
    'user_api_token_create' => 'Opprett API-nøkkel',
    'user_api_token_name' => 'Navn',
    'user_api_token_name_desc' => 'Gi nøkkelen et lesbart navn som en fremtidig påminnelse om det tiltenkte formålet.',
    'user_api_token_expiry' => 'Utløpsdato',
    'user_api_token_expiry_desc' => 'Angi en dato da denne nøkkelen utløper. Etter denne datoen vil forespørsler som er gjort med denne nøkkelen ikke lenger fungere. Å la dette feltet stå tomt vil sette utløpsdato 100 år inn i fremtiden.',
    'user_api_token_create_secret_message' => 'Umiddelbart etter å ha opprettet denne nøkkelen vil en identifikator og hemmelighet bli generert og vist. Hemmeligheten vil bare vises en gang, så husk å kopiere verdien til et trygt sted før du fortsetter.',
    'user_api_token_create_success' => 'API-nøkkel ble opprettet',
    'user_api_token_update_success' => 'API-nøkkel ble oppdatert',
    'user_api_token' => 'API-nøkkel',
    'user_api_token_id' => 'Identifikator',
    'user_api_token_id_desc' => 'Dette er en ikke-redigerbar systemgenerert identifikator for denne nøkkelen som må oppgis i API-forespørsler.',
    'user_api_token_secret' => 'Hemmelighet',
    'user_api_token_secret_desc' => 'Dette er en systemgenerert hemmelighet for denne nøkkelen som må leveres i API-forespørsler. Dette vises bare denne gangen, så kopier denne verdien til et trygt sted.',
    'user_api_token_created' => 'Nøkkel opprettet :timeAgo',
    'user_api_token_updated' => 'Nøkkel oppdatert :timeAgo',
    'user_api_token_delete' => 'Slett nøkkel',
    'user_api_token_delete_warning' => 'Dette vil slette API-nøkkelen \':tokenName\' fra systemet.',
    'user_api_token_delete_confirm' => 'Sikker på at du vil slette nøkkelen?',
    'user_api_token_delete_success' => 'API-nøkkelen ble slettet',

    //! If editing translations files directly please ignore this in all
    //! languages apart from en. Content will be auto-copied from en.
    //!////////////////////////////////
    'language_select' => [
        'en' => 'English',
        'ar' => 'العربية',
        'bg' => 'Bǎlgarski',
        'bs' => 'Bosanski',
        'ca' => 'Català',
        'cs' => 'Česky',
        'da' => 'Dansk',
        'de' => 'Deutsch (Sie)',
        'de_informal' => 'Deutsch (Du)',
        'es' => 'Español',
        'es_AR' => 'Español Argentina',
        'et' => 'Eesti keel',
        'fr' => 'Français',
        'he' => 'עברית',
        'hr' => 'Hrvatski',
        'hu' => 'Magyar',
        'id' => 'Bahasa Indonesia',
        'it' => 'Italian',
        'ja' => '日本語',
        'ko' => '한국어',
        'lt' => 'Lietuvių Kalba',
        'lv' => 'Latviešu Valoda',
        'nl' => 'Nederlands',
        'nb' => 'Norsk (Bokmål)',
        'pl' => 'Polski',
        'pt' => 'Português',
        'pt_BR' => 'Português do Brasil',
        'ru' => 'Русский',
        'sk' => 'Slovensky',
        'sl' => 'Slovenščina',
        'sv' => 'Svenska',
        'tr' => 'Türkçe',
        'uk' => 'Українська',
        'vi' => 'Tiếng Việt',
        'zh_CN' => '简体中文',
        'zh_TW' => '繁體中文',
    ],
    //!////////////////////////////////
];
