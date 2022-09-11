<?php

namespace Drupal\drupal_task_api\Plugin\rest\resource;

use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides a resource to get view modes by entity and bundle.
 * @RestResource(
 *   id = "continent_resource",
 *   label = @Translation("Get Drupal Continents"),
 *   uri_paths = {
 *     "canonical" = "/getDrupalContinents"
 *   }
 * )
 */
class ContinentResource extends ApiResourceAbstract
{
    /**
     * Responds to GET request.
     * Returns a list of taxonomy terms.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * Throws exception expected.
     */
    public function get()
    {
        // Implementing our custom REST Resource here.
        // Use currently logged user after passing authentication and validating the access of term list.
        if (!$this->loggedUser->hasPermission('access content')) {
            throw new AccessDeniedHttpException();
        }
        $vid = 'continents';
        $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
        foreach ($terms as $term) {
            $term_result[] = array(
                'id' => $term->tid,
                'name' => $term->name
            );
        }

        $response = new ResourceResponse($term_result);
        $response->addCacheableDependency($term_result);
        return $response;
    }
}
