<?php

/**
 * Plugin Name: Register Taxonomy - Portal Adventista
 * Plugin URI: https://www.adventistas.org
 * Description: Plugin que regista todas as taxonomias necessário para o funcionamento do portal.
 * Version: 1.1.1
 * Author: webdsa
 * Author URI: https://www.adventistas.org"
 **/


class PARegisterTax
{
  public function __construct()
  {
    add_action('after_setup_theme', array($this, 'installRoutines'), 8);
    add_filter("rest_post_query", array($this, 'filter_rest_post_query'), 10, 2);

    // Desativar o delete default de terms do WP
    add_filter("pre_delete_term", array($this, 'filter_delete_term'), 10, 2);

    // Inserindo Filtros de Lixeira e Ativos
    add_filter("get_terms", array($this, 'filter_get_terms'), 10, 4);

    // Action Restaurar
    add_filter("tag_row_actions", array($this, 'filter_actions'), 10, 2);
    add_action("admin_init", array($this, 'restore_term'));

    add_action('admin_head', function () {
      echo '<style>.edit-tags-php #wpbody-content .actions.bulkactions{display:none;}</style>';
      echo '<style>#wpbody-content .form-field.term-parent-wrap a{display:none;}</style>';
      echo '<style>#wpbody-content .edit-tag-actions #delete-link{display:none;}</style>';
    });
  }

  function installRoutines()
  {
    load_theme_textdomain('webdsa', plugin_dir_path(__FILE__) . 'language/');
    load_plugin_textdomain('webdsa', false, plugin_dir_path(__FILE__) . 'language/');

    $termos = array(
      'xtt-pa-colecoes'       => [__('Collections', 'webdsa'),            __('Collection', 'webdsa'),             false],
      'xtt-pa-editorias'      => [__('Editorials', 'webdsa'),             __('Editorial', 'webdsa'),              true],
      'xtt-pa-departamentos'  => [__('Ministries', 'webdsa'),             __('Ministry', 'webdsa'),               false],
      'xtt-pa-projetos'       => [__('Projects', 'webdsa'),               __('Project', 'webdsa'),                false],
      'xtt-pa-regiao'         => [__('Region', 'webdsa'),                 __('Regions', 'webdsa'),                false],
      'xtt-pa-sedes'          => [__('Regional Headquarters', 'webdsa'),  __('Regional Headquarter', 'webdsa'),   true],
      'xtt-pa-owner'          => [__('Owner Headquarter', 'webdsa'),      __('Owner Headquarter', 'webdsa'),      true],
      'xtt-pa-materiais'      => [__('File type', 'webdsa'),              __('File type', 'webdsa'),              false]
    );

    foreach ($termos as $key => $value) {

      $labels = array(
        'name'              => $value[0],
        'singular_name'     => $value[1],
        'search_items'      => __('Search', 'webdsa'),
        'all_items'         => __('All itens', 'webdsa'),
        'parent_item'       => $value[1] . ', father',
        'parent_item_colon' => $value[1] . ', father',
        'edit_item'         => __('Edit', 'webdsa'),
        'update_item'       => __('Update', 'webdsa'),
        'add_new_item'      => __('Add new', 'webdsa'),
        'new_item_name'     => __('New', 'webdsa'),
        'menu_name'         => $value[1],
      );

      $args   = array(
        'hierarchical'        => true, // make it hierarchical (like categories)
        'labels'              => $labels,
        'show_ui'             => true,
        'show_in_menu'        => current_user_can('administrator'),
        'show_admin_column'   => $value[2],
        'query_var'           => true,
        'rewrite'             => array('slug' => sanitize_title($value[1])),
        'public'              => true,
        'show_in_rest'        => true, // add support for Gutenberg editor
        // 'capabilities'        => array(
        //   'edit_terms'        => false,
        //   'delete_terms'      => false,
        // )
      );

      register_taxonomy($key, ['post'], $args);
    }
  }

  function filter_rest_post_query($args, $request)
  {
    $params = $request->get_params();


    if (isset($params['xtt-pa-owner-tax'])) {
      $args['tax_query'][] = array(
        array(
          'taxonomy' => 'xtt-pa-owner',
          'field' => 'slug',
          'terms' => explode(',', $params['xtt-pa-owner-tax']),
          'include_children' => false
        )
      );
    }

    if (isset($params['xtt-pa-departamentos-tax'])) {
      $args['tax_query'][] = array(
        array(
          'taxonomy' => 'xtt-pa-departamentos',
          'field' => 'slug',
          'terms' => explode(',', $params['xtt-pa-departamentos-tax'])
        )
      );
    }

    if (isset($params['xtt-pa-projetos-tax'])) {
      $args['tax_query'][] = array(
        array(
          'taxonomy' => 'xtt-pa-projetos',
          'field' => 'slug',
          'terms' => explode(',', $params['xtt-pa-projetos-tax'])
        )
      );
    }

    if (isset($params['xtt-pa-sedes-tax'])) {
      $args['tax_query'][] = array(
        array(
          'taxonomy' => 'xtt-pa-sedes',
          'field' => 'slug',
          'terms' => explode(',', $params['xtt-pa-sedes-tax']),
          'include_children' => false
        )
      );
    }

    if (isset($params['xtt-pa-editorias-tax'])) {
      $args['tax_query'][] = array(
        array(
          'taxonomy' => 'xtt-pa-editorias',
          'field' => 'slug',
          'terms' => explode(',', $params['xtt-pa-editorias-tax'])
        )
      );
    }

    return $args;
  }

  function filter_delete_term($term_id, $taxonomy)
  {

    $term_trash = get_term_meta($term_id, 'term_trash', true);
    $user = wp_get_current_user();
    $roles = (array) $user->roles;
    if (!$term_trash || ($term_trash && $roles[0] != 'administrator')) {
      add_term_meta($term_id, 'term_trash', true);
      die('1');
      wp_die('1');
    }
  }

  function filter_get_terms($terms, $taxonomy, $query_var, $term_query)
  {

    if (!isset($_GET['tag_ID'])) {
      $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

      $disableds = 0;
      $enableds = 0;

      foreach ($terms as $keyt => $term) {
        if (property_exists($term, 'term_id')) {
          $term_trash = get_term_meta($term->term_id, 'term_trash', true);

          if ($term_trash) {
            $terms[$keyt]->parent = 0;
            if (!isset($_GET['terms_trashed'])) {
              unset($terms[$keyt]);
            }
            $disableds++;
          } else {
            if (isset($_GET['terms_trashed'])) {
              unset($terms[$keyt]);
            }
            $enableds++;
          }
        }
      }

      if (strpos($actual_link, 'edit-tags.php?taxonomy=')) {
        $actual_link = str_replace('&terms_trashed=true', '', $actual_link);
        echo '<a href="' . $actual_link . '" style="position: absolute;margin-top: -30px;">Ativos (' . $enableds . ')</a>';
        echo '<a href="' . $actual_link . '&terms_trashed=true" style="position: absolute;margin-top: -30px;margin-left: 100px;">Lixeira (' . $disableds . ')</a>';
      }
    }

    return $terms;
  }

  function filter_actions($actions, $tag)
  {

    $term_id = $tag->term_id;

    if (isset($_GET['terms_trashed'])) {
      $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      $actual_link = str_replace('&restore_term=' . $term_id, '', $actual_link);

      $actions['restore'] = '<a href="' . $actual_link . '&restore_term=' . $term_id . '">Restaurar</a>';
    } else {
      $actions['delete'] = str_replace('Excluir', 'Lixeira', $actions['delete']);
    }

    return $actions;
  }

  function restore_term()
  {
    if (isset($_GET['restore_term'])) {
      $term_id = $_GET['restore_term'];
      update_term_meta($term_id, 'term_trash', false);
      $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      $actual_link = str_replace('&restore_term=' . $term_id, '', $actual_link);
      wp_redirect($actual_link);
    }
  }
}

$PARegisterTax = new PARegisterTax();
