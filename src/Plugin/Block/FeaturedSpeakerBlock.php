<?php

// featured_speaker/src/Plugin/Block/FeaturedSpeakerBlock.php
namespace Drupal\speaker_profile\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;


/**
 * Provides a 'Featured Speaker' block.
 *
 * @Block(
 *   id = "featured_speaker_block",
 *   admin_label = @Translation("Featured Speaker"),
 * )
 */
class FeaturedSpeakerBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    // Check if the content is already cached.
    if ($cache = \Drupal::cache()->get($this->getCacheKey())) {
      return $cache->data;
    }

    // Build and return the block content.
    $content = [
      '#theme' => 'speaker_profile',
      '#content' => $this->getFeaturedSpeakerContent(),
    ];

    // Cache the content for 24 hours.
    $cache_tags = ['featured_speaker'];
    $cache_max_age = 86400; // 24 hours
    $cache_contexts = ['url'];
    \Drupal::cache()->set($this->getCacheKey(), $content, Cache::PERMANENT, $cache_tags, $cache_max_age, $cache_contexts);

    return $content;
  }

  /**
   * Get a unique cache key for the block.
   *
   * @return string
   *   The cache key.
   */
  protected function getCacheKey()
  {
    return 'featured_speaker_block:' . date('Y-m-d');
  }

  /**
   * Get content for the featured speaker block.
   *
   * @return array
   *   An array containing information about the featured speaker.
   */
  protected function getFeaturedSpeakerContent()
  {
    // Implement logic to get a random speaker here.
    $randomSpeaker = $this->getRandomSpeaker();

    // Check if a random speaker is available.
    if ($randomSpeaker) {
      // Extract required fields (Name, Portrait, Topics of Expertise).
      $name = $randomSpeaker->label();
      $portrait = $randomSpeaker->get('portrait')->entity->getFileUri();
      $topics = $randomSpeaker->get('topics_expertise')->referencedEntities();
      $topicsLabels = array_map(function ($term) {
        return $term->label();
      }, $topics);

      // Return an array with the required fields.
      return [
        'name' => $name,
        'portrait' => $portrait,
        'topics' => implode(', ', $topicsLabels),
      ];
    }

    // Return an empty array if no speaker is found.
    return [];
  }
  protected function getRandomSpeaker() {
    // Get the entity type manager service.
    $entityTypeManager = \Drupal::entityTypeManager();

    // Specify the entity type for the speaker profile.
    $entityType = 'speaker_profile';

    // Get the storage for the speaker profile entities.
    $storage = $entityTypeManager->getStorage($entityType);

    // Query for speaker profile entities.
    $query = $storage->getQuery()
      ->condition('status', 1) // Assuming you have a 'status' field for speaker status.
      ->addTag('random_order') // Add a tag for random ordering.
      ->range(0, 1) // Limit to 1 result.
      ->accessCheck(false)
      ->execute();

    // Get the result.
    $result = reset($query);

    // Load the random speaker entity.
    $randomSpeaker = $result ? $storage->load($result) : null;

    return $randomSpeaker;
  }
}
