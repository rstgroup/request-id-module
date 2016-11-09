<?php
namespace RstGroup\RequestIdModule;

use PhpMiddleware\RequestId\Exception\MissingRequestId;
use PhpMiddleware\RequestId\Generator\GeneratorInterface;
use PhpMiddleware\RequestId\RequestIdProviderInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\MvcEvent;
use PhpMiddleware\RequestId\RequestIdProviderFactoryInterface;
use Zend\Http\Header\GenericHeader;
use Zend\Http\Response as HttpResponse;
use Zend\Psr7Bridge\Psr7ServerRequest;

final class RequestIdListener extends AbstractListenerAggregate implements RequestIdProviderInterface
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

    private $requestIdGenerator;

    public function __construct(RequestIdProviderFactoryInterface $requestIdProviderFactory,
                                $requestIdHeaderName = self::DEFAULT_REQUEST_ID_HEADER,
                                GeneratorInterface $requestIdGenerator = null)
    {
        $this->requestIdProviderFactory = $requestIdProviderFactory;
        $this->requestIdHeaderName = $requestIdHeaderName;
        $this->requestIdGenerator = $requestIdGenerator;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, [$this, 'loadRequestId']);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, [$this, 'addRequestIdToResponse']);
    }

    public function loadRequestId(MvcEvent $event)
    {
        $request = $event->getRequest();

        if ($request instanceof HttpRequest) {
            $psr7Request = Psr7ServerRequest::fromZend($request);
            $requestIdProvider = $this->requestIdProviderFactory->create($psr7Request);
            $this->requestId = $requestIdProvider->getRequestId();

        } elseif ($this->requestIdGenerator !== null) {
            $this->requestId = $this->requestIdGenerator->generateRequestId();
        }

        return $this->requestId;
    }

    public function getRequestId()
    {
        if ($this->requestId === null) {
            throw new MissingRequestId();
        }

        return $this->requestId;
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
    }
}