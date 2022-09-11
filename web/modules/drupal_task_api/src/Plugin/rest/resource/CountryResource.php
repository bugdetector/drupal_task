<?php

namespace Drupal\drupal_task_api\Plugin\rest\resource;

use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides a resource to get view modes by entity and bundle.
 * @RestResource(
 *   id = "country_one_resource",
 *   label = @Translation("Get Drupal Country by id"),
 *   uri_paths = {
 *     "canonical" = "/getDrupalCountry/{tid}"
 *   }
 * )
 */
class CountryResource extends ApiResourceAbstract
{
    /**
     * Responds to GET request.
     * Returns a list of taxonomy terms.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * Throws exception expected.
     */
    public function get($tid = null)
    {
        // Implementing our custom REST Resource here.
        // Use currently logged user after passing authentication and validating the access of term list.
        if (!$this->loggedUser->hasPermission('access content')) {
            throw new AccessDeniedHttpException();
        }
        $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid);
        if(!$term){
            throw new NotFoundHttpException();
        }
        $response = new ResourceResponse($term);
        $response->addCacheableDependency($term);
        return $response;
    }
}
