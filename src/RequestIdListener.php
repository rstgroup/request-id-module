<?php
namespace RstGroup\RequestIdModule;

use PhpMiddleware\RequestId\Exception\MissingRequestId;
use PhpMiddleware\RequestId\Generator\GeneratorInterface;
use PhpMiddleware\RequestId\RequestIdProviderInterface;
use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\MvcEvent;
use PhpMiddleware\RequestId\RequestIdProviderFactoryInterface;
use Laminas\Http\Header\GenericHeader;
use Laminas\Http\Response as HttpResponse;
use Laminas\Psr7Bridge\Psr7ServerRequest;

final class RequestIdListener extends AbstractListenerAggregate implements RequestIdProviderInterface
{
    const DEFAULT_REQUEST_ID_HEADER = 'X-Request-Id';

    private $requestId;
    private $requestIdHeaderName;
    private $requestIdProviderFactory;
    private $requestIdGenerator;

    public function __construct(
        RequestIdProviderFactoryInterface $requestIdProviderFactory,
        string $requestIdHeaderName = null,
        GeneratorInterface $requestIdGenerator = null
    ) {
        $this->requestIdProviderFactory = $requestIdProviderFactory;
        $this->requestIdHeaderName = $requestIdHeaderName ?? self::DEFAULT_REQUEST_ID_HEADER;
        $this->requestIdGenerator = $requestIdGenerator;
    }

    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, [$this, 'loadRequestId']);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, [$this, 'addRequestIdToResponse']);
    }

    public function loadRequestId(MvcEvent $event): ?string
    {
        $request = $event->getRequest();

        if ($request instanceof HttpRequest) {
            $psr7Request = Psr7ServerRequest::fromLaminas($request);
            $requestIdProvider = $this->requestIdProviderFactory->create($psr7Request);
            $this->requestId = $requestIdProvider->getRequestId();

        } elseif ($this->requestIdGenerator !== null) {
            $this->requestId = $this->requestIdGenerator->generateRequestId();
        }

        return $this->requestId;
    }

    public function getRequestId(): string
    {
        if ($this->requestId === null) {
            throw new MissingRequestId();
        }

        return $this->requestId;
    }

    public function addRequestIdToResponse(MvcEvent $event): void
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