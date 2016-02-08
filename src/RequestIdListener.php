<?php
namespace RstGroup\RequestIdModule;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;
use PhpMiddleware\RequestId\RequestIdProviderFactoryInterface;
use Zend\Http\Header\GenericHeader;
use Zend\Http\Response as HttpResponse;
use Zend\Psr7Bridge\Psr7ServerRequest;

final class RequestIdListener extends AbstractListenerAggregate
{
    const DEFAULT_REQUEST_ID_HEADER = 'X-Request-Id';

    /**
     * @var string
     */
    protected $requestId;

    /**
     * @var string
     */
    protected $requestIdHeaderName;

    protected $requestIdProviderFactory;

    public function __construct(RequestIdProviderFactoryInterface $requestIdProviderFactory, $requestIdHeaderName = self::DEFAULT_REQUEST_ID_HEADER)
    {
        $this->requestIdProviderFactory = $requestIdProviderFactory;
        $this->requestIdHeaderName = $requestIdHeaderName;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, [$this, 'getRequestId']);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, [$this, 'addRequestIdToResponse']);
    }

    public function getRequestId(MvcEvent $event)
    {
        $request = $event->getRequest();

        if (!$request instanceof HttpRequest) {
            return;
        }
        $psr7Request = Psr7ServerRequest::fromZend($request);

        $requestIdProvider = $this->requestIdProviderFactory->create($psr7Request);

        $this->requestId = $requestIdProvider->getRequestId();

        return $this->requestId ;
    }


    public function addRequestIdToResponse(MvcEvent $event)
    {
        $response = $event->getResponse();

        if (!$response instanceof HttpResponse) {
            return;
        }

        if ($this->requestId === null) {
            return;
        }

        $headers = $response->getHeaders();
        $headers->addHeader(new GenericHeader($this->requestIdHeaderName, $this->requestId));
;
    }
}