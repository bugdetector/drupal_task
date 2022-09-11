<?php


namespace Drupal\drupal_task_api\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class ApiResourceAbstract extends ResourceBase
{
    protected $apiUrl = "https://restcountries.com/v3.1/all";
    
    /**
     * A current user instance which is logged in the session.
     * @var \Drupal\Core\Session\AccountProxyInterface
     */
    protected $loggedUser;

    /**
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $currentRequest;

    public function __construct(
        array $config,
        $module_id,
        $module_definition,
        array $serializer_formats,
        LoggerInterface $logger,
        AccountProxyInterface $current_user,
        Request $current_request
    ) {
        parent::__construct($config, $module_id, $module_definition, $serializer_formats, $logger);

        $this->loggedUser = $current_user;
        $this->currentRequest = $current_request;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $config, $module_id, $module_definition)
    {
        return new static(
            $config,
            $module_id,
            $module_definition,
            $container->getParameter('serializer.formats'),
            $container->get('logger.factory')->get('task_resource'),
            $container->get('current_user'),
            $container->get('request_stack')->getCurrentRequest()
        );
    }

    protected function getOnlineCountries(){
        $client = new Client();
        /**
         * @var \Psr\Http\Message\StreamInterface
         */
        $apiResponse = $client->get($this->apiUrl)->getBody();
        return array_map(function($country){
            return [
                "name" => $country["name"]["common"]
            ];
        }, json_decode($apiResponse->__toString(), true));
    }

    protected function getTermList($vid) {
        $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
        foreach ($terms as $term) {
            $term_result[] = array(
                'id' => $term->tid,
                'name' => $term->name
            );
        }
        return $term_result;
    }
}
