<?php

namespace Drupal\drupal_task_api\Plugin\rest\resource;

use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides a resource to get view modes by entity and bundle.
 * @RestResource(
 *   id = "get_combined_countries_resource",
 *   label = @Translation("Get Combined Countries"),
 *   uri_paths = {
 *     "canonical" = "/getCombineCountries"
 *   }
 * )
 */
class GetCombinedCountriesResource extends ApiResourceAbstract
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
        
        $result = array_merge(
            $this->getTermList("countries"),
            $this->getOnlineCountries()
        );
        $response = new ResourceResponse($result);
        $response->addCacheableDependency($result);
        return $response;
    }
}
