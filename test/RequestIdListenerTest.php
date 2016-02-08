<?php
namespace RstGroup\RequestIdModule\Test;

use PhpMiddleware\RequestId\RequestIdProviderFactoryInterface;
use PhpMiddleware\RequestId\RequestIdProviderInterface;
use RstGroup\RequestIdModule\RequestIdListener;
use Zend\Console\Response as ConsoleResponse;
use Zend\Console\Request as ConsoleRequest;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;

class RequestIdListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $requestIdProviderFactoryInterface;

    protected $requestIdProviderInterface;

    public function setUp()
    {
        $this->requestIdProviderFactoryInterface = $this->getMock(RequestIdProviderFactoryInterface::class);
        $this->requestIdProviderInterface = $this->getMock(RequestIdProviderInterface::class);

        $this->requestIdProviderFactoryInterface->method('create')->willReturn($this->requestIdProviderInterface);
    }

    /**
     * @test
     */
    public function it_get_request_id()
    {
        $this->requestIdProviderInterface->method('getRequestId')->willReturn('abc123');

        $requestIdListener = new RequestIdListener($this->requestIdProviderFactoryInterface);

        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest(new HttpRequest());

        $requestIdListener->loadRequestId($mvcEvent);
        $requestId = $requestIdListener->getRequestId();

        $this->assertSame('abc123', $requestId);
    }

    /**
     * @test
     */
    public function it_add_request_id_to_response()
    {
        $this->requestIdProviderInterface->method('getRequestId')->willReturn('abc123');

        $requestIdListener = new RequestIdListener($this->requestIdProviderFactoryInterface);

        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest(new HttpRequest());

        $requestIdListener->loadRequestId($mvcEvent);

        $response = new HttpResponse();
        $mvcEvent->setResponse($response);

        $requestIdListener->addRequestIdToResponse($mvcEvent);

        $this->assertSame('abc123', $response->getHeaders()->get('X-Request-Id')->getFieldValue());
    }

    /**
     * @test
     */
    public function it_not_add_request_id_to_response_if_get_request_id()
    {
        $requestIdListener = new RequestIdListener($this->requestIdProviderFactoryInterface);

        $mvcEvent = new MvcEvent();
        $response = new HttpResponse();

        $mvcEvent->setResponse($response);

        $requestIdListener->addRequestIdToResponse($mvcEvent);

        $this->assertFalse($response->getHeaders()->get('X-Request-Id'));
    }

    /**
     * @test
     */
    public function it_get_request_id_if_not_http_request()
    {
        $requestIdListener = new RequestIdListener($this->requestIdProviderFactoryInterface);

        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest(new ConsoleRequest());

        $requestIdListener->loadRequestId($mvcEvent);
        $requestId = $requestIdListener->getRequestId();

        $this->assertNull($requestId);
    }

    /**
     * @test
     */
    public function it_not_add_request_id_to_response_if_not_http_response()
    {
        $requestIdListener = new RequestIdListener($this->requestIdProviderFactoryInterface);

        $mvcEvent = new MvcEvent();
        $mvcEvent->setResponse(new ConsoleResponse());

        $result = $requestIdListener->addRequestIdToResponse($mvcEvent);

        $this->assertNull($result);
    }
}