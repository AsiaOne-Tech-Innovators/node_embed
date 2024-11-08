<?php

namespace Drupal\node_embed\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Render\RenderContext;
use Drupal\Core\Render\RendererInterface;
use Drupal\entity_embed\EntityEmbedBuilderInterface;
use Drupal\entity_embed\Exception\EntityNotFoundException;
use Drupal\entity_embed\Exception\RecursiveRenderingException;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\embed\DomHelperTrait;
use Drupal\node\Entity\Node;
/**
 * Provides a filter to display embedded entities based on data attributes.
 *
 * @Filter(
 *   id = "entity_embed",
 *   title = @Translation("Display embedded entities"),
 *   description = @Translation("Embeds entities using data attributes: data-entity-type, data-entity-uuid, and data-view-mode."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE
 * )
 */
class NodeEmbedFilter extends FilterBase implements ContainerFactoryPluginInterface {


  const NODEEMBED_PATTERN_EMBED_SHORTCODE = '/\[\[nid:(\d+)(\s.*)?\]\]/';

  use DomHelperTrait;

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity embed builder service.
   *
   * @var \Drupal\entity_embed\EntityEmbedBuilderInterface
   */
  protected $builder;

  /**
   * Constructs a EntityEmbedFilter object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   * @param \Drupal\entity_embed\EntityEmbedBuilderInterface $builder
   *   The entity embed builder service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, RendererInterface $renderer, EntityEmbedBuilderInterface $builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->renderer = $renderer;
    $this->builder = $builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('renderer'),
      $container->get('entity_embed.builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {

    preg_match_all(self::NODEEMBED_PATTERN_EMBED_SHORTCODE, $text, $matches);
    
    if(!empty($matches[0])){
      $text = preg_replace_callback(self::NODEEMBED_PATTERN_EMBED_SHORTCODE, array($this, 'nodeembed_preg_tag_replace'), $text);
    }

    return new FilterProcessResult($text);   

  }

  public function nodeembed_preg_tag_replace($match){
    
    if($match[0]=='[[nid:'.$match[1].']]' && $match[1]){
      $entity_type = 'node';
      $view_mode = 'node_embed';
      $node = Node::load($match[1]);
      if($node && $node->isPublished()){
        $block_view = \Drupal::entityTypeManager()->getViewBuilder($entity_type)->view($node, $view_mode);
        
        if ($block_view) {
          // return drupal_render($block_view);
          return \Drupal::service('renderer')->render($block_view);
        }
      } 

    }

    

  }
  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    if ($long) {
      return $this->t('
        <p>You can embed entities. Additional properties can be added to the embed tag like data-caption and data-align if supported. Example:</p>
        <code>&lt;&lt;nid:1/&gt;/&gt;</code>');
    }
    else {
      return $this->t('You can embed entities.');
    }
  }

}
