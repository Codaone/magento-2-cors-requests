<?php

/**
 * @copyright  Copyright 2017 SplashLab
 */

namespace SplashLab\CorsRequests\Plugin;

use Magento\Framework\Controller\Router\Route\Factory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Webapi\Controller\Rest\Router;
use Magento\Webapi\Controller\Rest\Router\Route;

/**
 * Class CorsRequestMatchPlugin
 *
 * @package SplashLab\CorsRequests
 */
class CorsRequestMatchPlugin
{
    private Request $request;
    protected Factory $routeFactory;

    /**
     * Initialize dependencies.
     *
     * @param Request $request
     * @param Factory $routeFactory
     */
    public function __construct(
        Request $request,
        Factory $routeFactory
    ) {
        $this->request = $request;
        $this->routeFactory = $routeFactory;
    }

    /**
     * Generate the list of available REST routes. Current HTTP method is taken into account.
     *
     * @param Router $subject
     * @param callable $proceed
     * @param Request $request
     * @return Route
     * @throws Exception
     * @throws InputException
     */
    public function aroundMatch(
        Router $subject,
        callable $proceed,
        Request $request
    ) {
        try {
            $returnValue = $proceed($request);
        } catch (Exception $e) {
            $requestHttpMethod = $this->request->getHttpMethod();
            if ($requestHttpMethod == 'OPTIONS') {
                return $this->createRoute();
            } else {
                throw $e;
            }
        }
        return $returnValue;
    }

    /**
     * Create route object to the placeholder CORS route.
     *
     * @return Route
     */
    protected function createRoute()
    {
        /** @var $route Route */
        $route = $this->routeFactory->createRoute(
            'Magento\Webapi\Controller\Rest\Router\Route',
            '/V1/cors/check'
        );

        $route->setServiceClass('SplashLab\CorsRequests\Api\CorsCheckInterface')
            ->setServiceMethod('check')
            ->setSecure(false)
            ->setAclResources(['anonymous'])
            ->setParameters([]);

        return $route;
    }

}
