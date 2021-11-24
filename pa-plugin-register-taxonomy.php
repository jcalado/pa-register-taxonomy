<?php

/**
 * Plugin Name: Register Taxonomy - Portal Adventista
 * Plugin URI: https://www.adventistas.org
 * Description: Plugin que regista todas as taxonomias necessário para o funcionamento do portal.
 * Version: 1.1
 * Author: webdsa
 * Author URI: https://www.adventistas.org"
 **/


class PARegisterTax
{
  public function __construct()
  {
    add_action('after_setup_theme', array($this, 'installRoutines'), 8);
    add_filter("rest_post_query", array($this, 'filter_rest_post_query'), 10, 2);
  }

  function installRoutines()
  {
    load_theme_textdomain('webdsa', plugin_dir_path(__FILE__) . 'language/');
    load_plugin_textdomain('webdsa', false, plugin_dir_path(__FILE__) . 'language/');

    $termos = array(
      'xtt-pa-colecoes'       => [__('Collections', 'webdsa'),            __('Collection', 'webdsa'),           false],
      'xtt-pa-editorias'      => [__('Editorials', 'webdsa'),             __('Editorial', 'webdsa'),            true],
      'xtt-pa-departamentos'  => [__('Ministries', 'webdsa'),             __('Ministry', 'webdsa'),             false],
      'xtt-pa-projetos'       => [__('Projects', 'webdsa'),               __('Project', 'webdsa'),              false],
      'xtt-pa-regiao'         => [__('Region', 'webdsa'),                 __('Regions', 'webdsa'),              false],
      'xtt-pa-sedes'          => [__('Regional Headquarters', 'webdsa'),  __('Regional Headquarter', 'webdsa'), true],
      'xtt-pa-owner'          => [__('Owner Headquarter', 'webdsa'),      __('Owner Headquarter', 'webdsa'),    true],
      'xtt-pa-kits'           => [__('Kit', 'webdsa'),                    __('Kits', 'webdsa'),                 false],
      'xtt-pa-materiais'      => [__('File type', 'webdsa'),              __('File type', 'webdsa'),            false]
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
        'show_admin_column'   => $value[2],
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
}

$PARegisterTax = new PARegisterTax();
