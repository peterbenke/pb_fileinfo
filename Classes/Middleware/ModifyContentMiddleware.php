<?php

namespace PeterBenke\PbFileinfo\Middleware;

/**
 * PbFileinfo
 */

use PeterBenke\PbFileinfo\Service\ModifyContentService;

/**
 * Psr
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * TYPO3
 */

use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * ModifyContentMiddleware
 * @package PeterBenke\PbRelNofollow\Middleware
 */
class ModifyContentMiddleware implements MiddlewareInterface
{

    /**
     * @var ModifyContentService
     */
    protected ModifyContentService $modifyContentService;

    /**
     * ModifyContentMiddleware constructor
     */
    public function __construct()
    {
        $this->modifyContentService = GeneralUtility::makeInstance(ModifyContentService::class);
    }

    /**
     * Modify the content
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $response = $handler->handle($request);

        if (
            !($response instanceof NullResponse)
            &&
            $GLOBALS['TSFE'] instanceof TypoScriptFrontendController
        ) {

            $modifiedHtml = $this->modifyContentService->clean(
                $response->getBody()->__toString(),
                $GLOBALS['TSFE']->config['config']['pb_fileinfo.']
            );

            $responseBody = new Stream('php://temp', 'rw');
            $responseBody->write($modifiedHtml);

            $response = $response->withBody($responseBody);

        }

        return $response;

    }

}