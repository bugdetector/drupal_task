<?php

namespace Drupal\drupal_task_api\Plugin\rest\resource;

use Drupal\rest\ResourceResponse;
use GuzzleHttp\Client;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides a resource to get view modes by entity and bundle.
 * @RestResource(
 *   id = "get_online_countries_resource",
 *   label = @Translation("Get Online Countries"),
 *   uri_paths = {
 *     "canonical" = "/getOnlineCountries"
 *   }
 * )
 */
class GetOnlineCountriesResource extends ApiResourceAbstract
{

    protected $apiUrl = "https://restcountries.com/v3.1/all";

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
        $client = new Client();
        /**
         * @var \Psr\Http\Message\StreamInterface
         */
        $apiResponse = $client->get($this->apiUrl)->getBody();
        $result = array_map(function($country){
            return [
                "name" => $country["name"]["common"]
            ];
        }, json_decode($apiResponse->__toString(), true));
        $response = new ResourceResponse($result);
        $response->addCacheableDependency($result);
        return $response;
    }
}
