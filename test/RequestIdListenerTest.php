<?php
namespace RstGroup\RequestIdModule\Test;

use PhpMiddleware\RequestId\Exception\MissingRequestId;
use PhpMiddleware\RequestId\Generator\GeneratorInterface;
use PhpMiddleware\RequestId\RequestIdProviderFactoryInterface;
use PhpMiddleware\RequestId\RequestIdProviderInterface;
use PHPUnit\Framework\TestCase;
use RstGroup\RequestIdModule\RequestIdListener;
use Zend\Console\Response as ConsoleResponse;
use Zend\Console\Request as ConsoleRequest;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;

class RequestIdListenerTest extends TestCase
{
    protected $requestIdProviderFactoryInterface;

    protected $requestIdProviderInterface;

    public function setUp()
    {
        $this->requestIdProviderFactoryInterface = $this->createMock(RequestIdProviderFactoryInterface::class);
        $this->requestIdProviderInterface = $this->createMock(RequestIdProviderInterface::class);

        $this->requestIdProviderFactoryInterface->method('create')->willReturn($this->requestIdProviderInterface);
    }

    /**
     * @test
     */
    public function it_load_request_id()
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
    public function it_not_add_request_id_to_response_after_load_request_id()
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
    public function it_not_load_request_id_if_not_http_request_and_generator_is_undefined()
    {
        $requestIdListener = new RequestIdListener($this->requestIdProviderFactoryInterface);

        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest(new ConsoleRequest());



        $requestIdListener->loadRequestId($mvcEvent);

        $this->expectException(MissingRequestId::class);

        $requestIdListener->getRequestId();
    }

    /**
     * @test
     */
    public function it_create_request_id_if_not_http_request()
    {
        $requestId = 'e5dd58f4-b72d-4d7e-b0c9-d99040386a58';
        $requestIdGenerator = $this->createMock(GeneratorInterface::class);
        $requestIdGenerator->method('generateRequestId')->willReturn($requestId);

        $requestIdListener = new RequestIdListener($this->requestIdProviderFactoryInterface, RequestIdListener::DEFAULT_REQUEST_ID_HEADER, $requestIdGenerator);

        $mvcEvent = new MvcEvent();
        $mvcEvent->setRequest(new ConsoleRequest());

        $requestIdListener->loadRequestId($mvcEvent);

        $this->assertSame($requestId, $requestIdListener->getRequestId());
    }

    /**
     * @test
     */
    public function it_not_add_request_id_to_response_if_not_http_response()
    {
        $requestIdListener = new RequestIdListener($this->requestIdProviderFactoryInterface);

        $response = new ConsoleResponse();
        $responseCopy = clone $response;
        $mvcEvent = new MvcEvent();
        $mvcEvent->setResponse($response);

        $requestIdListener->addRequestIdToResponse($mvcEvent);

        $this->assertEquals($responseCopy, $response);
    }

    /**
     * @test
     */
    public function it_throw_excpetion_if_request_id_is_not_set()
    {
        $requestIdListener = new RequestIdListener($this->requestIdProviderFactoryInterface);

        $this->expectException(MissingRequestId::class);

        $requestIdListener->getRequestId();
    }
}