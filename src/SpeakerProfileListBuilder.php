<?php declare(strict_types = 1);

namespace Drupal\speaker_profile;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a list controller for the speaker profile entity type.
 */
final class SpeakerProfileListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    $header['id'] = $this->t('ID');
    $header['label'] = [
      'data' => $this->t('Name'),
      'field' => 'label',
      'specifier' => 'label',
      'class' => ['sortable'],
    ];
    $header['topics_expertise'] = [
      'data' => $this->t('Expertise'),
      'field' => 'topics_expertises',
      'specifier' => 'label',
      'class' => ['sortable'],
    ];
//    $header['status'] = $this->t('Status');
    $header['uid'] = $this->t('Author');
    $header['created'] = $this->t('Created');
    $header['changed'] = $this->t('Updated');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    /** @var \Drupal\speaker_profile\SpeakerProfileInterface $entity */
    $row['id'] = $entity->id();
    $row['label'] = $entity->toLink();

    $topics_expertise = $entity->get('topics_expertise')->referencedEntities();
    $topics_expertise_labels = array_map(function ($term) {
      return $term->label();
    }, $topics_expertise);

    $row['topics_expertise'] = !empty($topics_expertise_labels) ? implode(', ', $topics_expertise_labels) : $this->t('N/A');
    $username_options = [
      'label' => 'hidden',
      'settings' => ['link' => $entity->get('uid')->entity->isAuthenticated()],
    ];
    $row['uid']['data'] = $entity->get('uid')->view($username_options);
    $row['created']['data'] = $entity->get('created')->view(['label' => 'hidden']);
    $row['changed']['data'] = $entity->get('changed')->view(['label' => 'hidden']);
    return $row + parent::buildRow($entity);
  }

}
