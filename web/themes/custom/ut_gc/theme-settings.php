<?php

/**
 * @file
 * Impostazioni del tema: logo, etichette e testi di header e footer.
 *
 * I link (Info per, menu di servizio, menu principale, social, menu del
 * footer) si gestiscono invece da Struttura > Menu.
 */

use Drupal\Core\File\FileExistsBehavior;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function ut_gc_form_system_theme_settings_alter(&$form, FormStateInterface $form_state, $form_id = NULL) {
  // Drupal chiama questa funzione sia dal form (senza $form_id) sia come
  // hook_form_alter del tema attivo (con $form_id): basta la prima.
  if (isset($form_id)) {
    return;
  }
  // Il tema in configurazione (ut_gc o un suo sottotema come ut_gc_lite).
  $theme = $form_state->getBuildInfo()['args'][0] ?? 'ut_gc';

  $menu_links = [];
  foreach (['gc-info-per' => 'Info per', 'gc-servizio' => 'Menu di servizio', 'main' => 'Menu principale', 'gc-social' => 'Social', 'gc-contatti' => 'Footer: contatti', 'gc-organizzazione' => 'Footer: organizzazione', 'gc-riferimenti' => 'Footer: riferimenti', 'gc-portale' => 'Footer: portale', 'gc-quicklinks' => 'Footer: quick links'] as $menu => $label) {
    $url = Url::fromRoute('entity.menu.edit_form', ['menu' => $menu]);
    $menu_links[] = ['#type' => 'link', '#title' => $label, '#url' => $url, '#wrapper_attributes' => []];
  }

  $form['ut_gc'] = [
    '#type' => 'vertical_tabs',
    '#title' => t('Grafica coordinata'),
    '#weight' => -100,
  ];

  $form['ut_gc_menus'] = [
    '#type' => 'details',
    '#title' => t('Link di header e footer'),
    '#group' => 'ut_gc',
    'help' => [
      '#markup' => '<p>' . t("Le voci dei menu si modificano da Struttura &gt; Menu. Classe CSS, target e title dei singoli link si impostano nel campo <em>Attributi</em> di ogni voce. Un menu vuoto non viene mostrato.") . '</p>',
    ],
    'links' => [
      '#theme' => 'item_list',
      '#items' => $menu_links,
    ],
  ];

  // Header.
  $form['ut_gc_header'] = [
    '#type' => 'details',
    '#title' => t('Header'),
    '#group' => 'ut_gc',
    '#description' => t("L'immagine del logo si carica nella sezione <em>Impostazioni del logo</em> più in basso."),
  ];
  $form['ut_gc_header']['gc_logo_link'] = [
    '#type' => 'radios',
    '#title' => t('Cliccando sul logo si va'),
    '#options' => [
      'front' => t('alla home di questo sito'),
      'custom' => t('a un indirizzo a scelta'),
      'none' => t('da nessuna parte (il logo non è un link)'),
    ],
    '#default_value' => theme_get_setting('gc_logo_link', $theme) ?: 'custom',
  ];
  $form['ut_gc_header']['gc_logo_url'] = [
    '#type' => 'textfield',
    '#title' => t('Indirizzo del link del logo'),
    '#description' => t('Per esempio <code>https://www.units.it/</code>.'),
    '#default_value' => theme_get_setting('gc_logo_url', $theme),
    '#states' => [
      'visible' => [':input[name="gc_logo_link"]' => ['value' => 'custom']],
      'required' => [':input[name="gc_logo_link"]' => ['value' => 'custom']],
    ],
  ];
  $form['ut_gc_header']['gc_logo_title'] = [
    '#type' => 'textfield',
    '#title' => t('Title del link del logo'),
    '#default_value' => theme_get_setting('gc_logo_title', $theme),
  ];
  $form['ut_gc_header']['gc_logo_alt'] = [
    '#type' => 'textfield',
    '#title' => t('Testo alternativo del logo'),
    '#default_value' => theme_get_setting('gc_logo_alt', $theme),
    '#required' => TRUE,
  ];
  $form['ut_gc_header']['gc_logo_size'] = [
    '#type' => 'container',
    '#attributes' => ['class' => ['container-inline']],
    'gc_logo_width' => [
      '#type' => 'number',
      '#title' => t('Larghezza logo (px)'),
      '#min' => 0,
      '#default_value' => theme_get_setting('gc_logo_width', $theme),
    ],
    'gc_logo_height' => [
      '#type' => 'number',
      '#title' => t('Altezza logo (px)'),
      '#min' => 0,
      '#default_value' => theme_get_setting('gc_logo_height', $theme),
    ],
  ];
  $form['ut_gc_header']['gc_info_per_label'] = [
    '#type' => 'textfield',
    '#title' => t('Etichetta del menu a tendina "Info per"'),
    '#default_value' => theme_get_setting('gc_info_per_label', $theme),
  ];
  $form['ut_gc_header']['gc_language_switcher'] = [
    '#type' => 'checkbox',
    '#title' => t('Mostra il cambio lingua (ITA/ENG)'),
    '#description' => t('Compare solo se il sito ha più di una lingua.'),
    '#default_value' => theme_get_setting('gc_language_switcher', $theme),
  ];

  // Ricerca.
  $form['ut_gc_search'] = [
    '#type' => 'details',
    '#title' => t('Ricerca'),
    '#group' => 'ut_gc',
  ];
  $form['ut_gc_search']['gc_search_enabled'] = [
    '#type' => 'checkbox',
    '#title' => t('Mostra la lente e la finestra di ricerca'),
    '#default_value' => theme_get_setting('gc_search_enabled', $theme),
  ];
  $search_visible = ['visible' => [':input[name="gc_search_enabled"]' => ['checked' => TRUE]]];
  $form['ut_gc_search']['gc_search_action'] = [
    '#type' => 'url',
    '#title' => t('Indirizzo del motore di ricerca'),
    '#default_value' => theme_get_setting('gc_search_action', $theme),
    '#states' => $search_visible,
  ];
  $form['ut_gc_search']['gc_search_param'] = [
    '#type' => 'textfield',
    '#title' => t('Nome del parametro di ricerca'),
    '#default_value' => theme_get_setting('gc_search_param', $theme),
    '#size' => 10,
    '#states' => $search_visible,
  ];
  $form['ut_gc_search']['gc_search_placeholder'] = [
    '#type' => 'textfield',
    '#title' => t('Testo segnaposto'),
    '#default_value' => theme_get_setting('gc_search_placeholder', $theme),
    '#states' => $search_visible,
  ];
  $form['ut_gc_search']['gc_search_links'] = [
    '#type' => 'textarea',
    '#title' => t('Link sotto la ricerca'),
    '#description' => t('Uno per riga, nel formato <code>Etichetta|https://indirizzo</code>.'),
    '#default_value' => theme_get_setting('gc_search_links', $theme),
    '#rows' => 3,
    '#states' => $search_visible,
  ];

  // Footer.
  $form['ut_gc_footer'] = [
    '#type' => 'details',
    '#title' => t('Footer'),
    '#group' => 'ut_gc',
  ];
  $footer_logo_path = theme_get_setting('gc_footer_logo_path', $theme);
  $form['ut_gc_footer']['gc_footer_logo_path'] = [
    '#type' => 'textfield',
    '#title' => t('Percorso del logo del footer'),
    '#description' => t('Vuoto per usare il logo bianco della grafica coordinata. Accetta un URI come <code>public://logo-footer.svg</code> o un file caricato qui sotto.'),
    '#default_value' => $footer_logo_path,
  ];
  $form['ut_gc_footer']['gc_footer_logo_upload'] = [
    '#type' => 'file',
    '#title' => t('Carica il logo del footer'),
    '#description' => t('SVG, PNG o JPG. Su sfondo blu: meglio una versione bianca.'),
    '#upload_validators' => [
      'FileExtension' => ['extensions' => 'svg png jpg jpeg webp'],
    ],
  ];
  $form['ut_gc_footer']['gc_footer_logo_alt'] = [
    '#type' => 'textfield',
    '#title' => t('Testo alternativo del logo del footer'),
    '#default_value' => theme_get_setting('gc_footer_logo_alt', $theme),
  ];
  $address = theme_get_setting('gc_footer_address', $theme) ?: [];
  $form['ut_gc_footer']['gc_footer_address'] = [
    '#type' => 'text_format',
    '#title' => t('Indirizzo e recapiti'),
    '#default_value' => $address['value'] ?? '',
    '#format' => $address['format'] ?? 'basic_html',
    '#rows' => 4,
  ];
  $form['ut_gc_footer']['gc_anvur_enabled'] = [
    '#type' => 'checkbox',
    '#title' => t('Mostra il riquadro "Fascia A ANVUR"'),
    '#default_value' => theme_get_setting('gc_anvur_enabled', $theme),
  ];
  $anvur_visible = ['visible' => [':input[name="gc_anvur_enabled"]' => ['checked' => TRUE]]];
  $form['ut_gc_footer']['gc_anvur_label'] = [
    '#type' => 'textfield',
    '#title' => t('Etichetta del riquadro'),
    '#default_value' => theme_get_setting('gc_anvur_label', $theme),
    '#states' => $anvur_visible,
  ];
  $form['ut_gc_footer']['gc_anvur_url'] = [
    '#type' => 'textfield',
    '#title' => t('Link del riquadro'),
    '#default_value' => theme_get_setting('gc_anvur_url', $theme),
    '#states' => $anvur_visible,
  ];

  // FormBuilder aggiunge ::validateForm e ::submitForm dopo gli alter, quindi
  // questi handler girano prima di quelli core (che salvano tutti i valori).
  // Variante Lite: niente Info per, ricerca, ANVUR e menu del tema; il footer
  // ha una regione libera per i blocchi.
  if (theme_get_setting('gc_lite', $theme)) {
    unset(
      $form['ut_gc_menus'],
      $form['ut_gc_search'],
      $form['ut_gc_header']['gc_info_per_label'],
      $form['ut_gc_header']['gc_language_switcher'],
      $form['ut_gc_footer']['gc_anvur_enabled'],
      $form['ut_gc_footer']['gc_anvur_label'],
      $form['ut_gc_footer']['gc_anvur_url'],
    );
    $form['ut_gc_footer']['#description'] = t('Link e altri contenuti del footer: Struttura &gt; Layout dei blocchi, regione <em>Footer</em>.');
  }

  $form['#validate'][] = 'ut_gc_theme_settings_validate';
  $form['#submit'] = array_merge(['ut_gc_theme_settings_submit'], $form['#submit'] ?? []);
}

/**
 * Validazione: link del logo e caricamento del logo del footer.
 */
function ut_gc_theme_settings_validate(&$form, FormStateInterface $form_state) {
  if ($form_state->getValue('gc_logo_link') === 'custom' && trim((string) $form_state->getValue('gc_logo_url')) === '') {
    $form_state->setErrorByName('gc_logo_url', t("Indica l'indirizzo del link del logo."));
  }

  $file = _file_save_upload_from_form($form['ut_gc_footer']['gc_footer_logo_upload'], $form_state, 0);
  if ($file) {
    $form_state->set('gc_footer_logo_file', $file);
  }

  $path = trim((string) $form_state->getValue('gc_footer_logo_path'));
  if ($path !== '' && !$file && !file_exists($path) && !file_exists(DRUPAL_ROOT . '/' . ltrim($path, '/'))) {
    $form_state->setErrorByName('gc_footer_logo_path', t('Il file del logo del footer non esiste.'));
  }
}

/**
 * Submit: copia il logo caricato in public:// e ne salva il percorso.
 */
function ut_gc_theme_settings_submit(&$form, FormStateInterface $form_state) {
  $form_state->unsetValue('gc_footer_logo_upload');
  $file = $form_state->get('gc_footer_logo_file');
  if ($file) {
    /** @var \Drupal\Core\File\FileSystemInterface $file_system */
    $file_system = \Drupal::service('file_system');
    $destination = 'public://ut_gc';
    $file_system->prepareDirectory($destination, FileSystemInterface::CREATE_DIRECTORY);
    $uri = $file_system->copy($file->getFileUri(), $destination . '/' . $file->getFilename(), FileExistsBehavior::Replace);
    $form_state->setValue('gc_footer_logo_path', $uri);
  }
}
