<?php
/**
 * @copyright  Copyright 2017 SplashLab
 */

namespace SplashLab\CorsRequests\Model;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Webapi\Rest\Response;
use SplashLab\CorsRequests\Api\CorsCheckInterface;

/**
 * Class CorsCheck
 * @package SplashLab\CorsRequests\Model
 */
class CorsCheck implements CorsCheckInterface
{
    protected Response $response;
    protected Request $request;

    /**
     * @param Response $response
     * @param Request $request
     */
    public function __construct(
        Response $response,
        Request $request
    ) {
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * {@inheritDoc}
     */
    public function check()
    {
        // respond to OPTIONS request with appropriate headers
        $this->response->setHeader('Access-Control-Allow-Methods', $this->request->getHeader('Access-Control-Request-Method'), true);
        $this->response->setHeader('Access-Control-Allow-Headers', $this->request->getHeader('Access-Control-Request-Headers'), true);
        return '';
    }

}
