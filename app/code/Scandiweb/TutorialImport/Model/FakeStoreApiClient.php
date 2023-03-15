<?php declare(strict_types=1);
namespace Scandiweb\TutorialImport\Model;

use Magento\Framework\App\Config;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Json\Helper\Data;
use Zend_Http_Client;

class FakeStoreApiClient
{
    // ...

    /** @var ZendClientFactory */
    protected $zendClientFactory;

    /** @var Data */
    protected $jsonHelper;

    /** @var Config */
    protected $config;
    protected ZendClient $zendClient;
    /**
     * FakeStoreApiClient constructor.
     * @param ZendClientFactory $zendClientFactory
     * @param ZendClient $zendClient
     * @param Data $jsonHelper
     * @param Config $config
     */
    public function __construct(ZendClient $zendClient, ZendClientFactory $zendClientFactory, Data $jsonHelper, Config $config)
    {
        $this->zendClientFactory = $zendClientFactory;
        $this->zendClient = $zendClient;
        $this->jsonHelper = $jsonHelper;
        $this->config = $config;
    }
    // the base URL of our API
    const API_URL = 'https://fakestoreapi.com/';

    // the endpoint for fetching products
    const RESOURCE_PRODUCTS = 'products';

    // the configuration key
    // this is what we'll use to get the configuration we created in the previous step.
    const API_KEY_CONFIG_PATH = 'tutorial_import/api/auth_token';

    protected function getApiToken()
    {
        // get the API Token from the configuration
        return $this->config->getValue(self::API_KEY_CONFIG_PATH);
    }

    /**
     * @param $uri
     * @param $method
     * @return Zend_Http_Response
     * @throws Zend_Http_Client_Exception
     */
    protected function makeRequest($uri, $method)
    {
        // create a new client
        /** @var ZendClient $zendClient */
        $zendClient = $this->zendClientFactory->create();
        $token = $this->getApiToken();

        // configure the request by specifying the URL,
        // HTTP method (get/post), as well as any headers the API might need
        $zendClient
            ->setUri(sprintf("%s%s", self::API_URL, $uri))
            ->setMethod($method)
            ->setHeaders([
                'Accept: application/json',
                sprintf("Authorization: Bearer %s", $token)
            ]);

        // make the request and return the response
        return $zendClient->request();
    }
    public function requestProducts()
    {
        $response =  $this->makeRequest(self::RESOURCE_PRODUCTS, ZendClient::GET);
        return $this->decodeResponse($response);
    }

    protected function decodeResponse($response)
    {
        return $this->jsonHelper->jsonDecode($response->getBody());
    }
}
