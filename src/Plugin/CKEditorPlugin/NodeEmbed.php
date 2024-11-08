<?php

namespace Drupal\node_embed\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "NodeEmbed" plugin.
 *
 * @CKEditorPlugin(
 *   id = "nodeembed",
 *   label = @Translation("Node Embed"),
 * )
 */
class NodeEmbed extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return \Drupal::service('extension.list.module')->getPath('node_embed') . '/js/plugins/NodeEmbed/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return array(
      'core/drupal.ajax',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return array(
      'NodeEmbed_dialogTitleAdd' => t('Add Link'),
      'NodeEmbed_dialogTitleEdit' => t('Edit Link'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    $path = \Drupal::service('extension.list.module')->getPath('node_embed') . '/js/plugins/NodeEmbed';
    return array(
      'NodeEmbed' => array(
        'label' => t('Node Embed Insert'),
        'image' => $path . '/node_embed.png',
      ),
    );
  }

}
