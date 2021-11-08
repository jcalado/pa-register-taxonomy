<?php

/**
 * Plugin Name: Register Taxonomy - Portal Adventista
 * Plugin URI: https://www.adventistas.org
 * Description: Plugin que regista todas as taxonomias necessário para o funcionamento do portal.
 * Version: 1.0
 * Author: webdsa
 * Author URI: https://www.adventistas.org"
 **/


class PARegisterTax
{
  public function __construct()
  {
    add_action('after_setup_theme', array($this, 'installRoutines'), 8);
  }

  function installRoutines()
  {
    load_theme_textdomain('webdsa', plugin_dir_path(__FILE__) . 'language/');
    load_plugin_textdomain('webdsa', plugin_dir_path(__FILE__) . 'language/');

    $termos = array(
      'xtt-pa-colecoes'       => [__('Collections', 'webdsa'),            __('Collection', 'webdsa')],
      'xtt-pa-editorias'      => [__('Editorials', 'webdsa'),             __('Editorial', 'webdsa')],
      'xtt-pa-departamentos'  => [__('Ministries', 'webdsa'),             __('Ministry', 'webdsa')],
      'xtt-pa-projetos'       => [__('Projects', 'webdsa'),               __('Project', 'webdsa')],
      'xtt-pa-regiao'         => [__('Region', 'webdsa'),                 __('Regions', 'webdsa')],
      'xtt-pa-sedes'          => [__('Regional Headquarters', 'webdsa'),  __('Regional Headquarter', 'webdsa')],
      'xtt-pa-owner'          => [__('Owner Headquarter', 'webdsa'),      __('Owner Headquarter', 'webdsa')]
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
        'show_admin_column'   => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => sanitize_title($value[1])),
        'public'              => true,
        'show_in_rest'        => true, // add support for Gutenberg editor
        'capabilities'        => array(
          'edit_terms'        => false,
          'delete_terms'      => false,
        )
      );

      register_taxonomy($key, ['post'], $args);
    }
  }
}

$PARegisterTax = new PARegisterTax();
