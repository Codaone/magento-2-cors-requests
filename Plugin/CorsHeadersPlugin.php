<?php

/**
 * @copyright  Copyright 2017 SplashLab
 */

namespace SplashLab\CorsRequests\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Webapi\Rest\Response;
use Magento\Store\Model\ScopeInterface;

/**
 * Class CorsHeadersPlugin
 *
 * @package SplashLab\CorsRequests
 */
class CorsHeadersPlugin
{
    private Response $response;
    private ScopeConfigInterface $scopeConfig;

    /**
     * Initialize dependencies.
     *
     * @param Response $response
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Response $response,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->response = $response;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get the origin domain the requests are going to come from
     * @return string
     */
    protected function getOriginUrl()
    {
        return $this->scopeConfig->getValue('web/corsRequests/origin_url',
            ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get the origin domain the requests are going to come from
     * @return bool
     */
    protected function getAllowCredentials()
    {
        return (bool) $this->scopeConfig->getValue(
            'web/corsRequests/allow_credentials',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get the origin domain the requests are going to come from
     * @return bool
     */
    protected function getEnableAmp()
    {
        return (bool) $this->scopeConfig->getValue(
            'web/corsRequests/enable_amp',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get the Access-Control-Max-Age
     * @return int
     */
    protected function getMaxAge()
    {
        return (int) $this->scopeConfig->getValue(
            'web/corsRequests/max_age',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Triggers before original dispatch
     * This method triggers before original \Magento\Webapi\Controller\Rest::dispatch and set version
     * from request params to VersionManager instance
     * @param FrontControllerInterface $subject
     * @param RequestInterface $request
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDispatch(
        FrontControllerInterface $subject,
        RequestInterface $request
    ) {
        if ($originUrl = $this->getOriginUrl()) {
            $this->response->setHeader('Access-Control-Allow-Origin', rtrim($originUrl,"/"), true);
            if ($this->getAllowCredentials()) {
                $this->response->setHeader('Access-Control-Allow-Credentials', 'true', true);
            }
            if ($this->getEnableAmp()) {
                $this->response->setHeader('AMP-Access-Control-Allow-Source-Origin', rtrim($originUrl,"/"), true);
            }
            if ((int)$this->getMaxAge() > 0) {
                $this->response->setHeader('Access-Control-Max-Age', $this->getMaxAge(), true);
            }
        }
    }

}
