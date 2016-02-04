<?php
namespace RstGroup\RequestIdModule\Test;

use PhpMiddleware\RequestId\Generator\GeneratorInterface;
use Zend\Http\Response;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\Router\RouteStackInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\DispatchableInterface;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class RequestIdIntegrationTest extends AbstractHttpControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->setApplicationConfig(
            include __DIR__ . '/TestAsset/application.config.php'
        );
    }

    public function tearDown()
    {
        unset($_SERVER['HTTP_X_REQUEST_ID']);

        return parent::tearDown();
    }

    /**
     * @test
     */
    public function it_return_response_200_with_generated_request_id_if_controller_return_new_response()
    {
        $this->mockGenerator('abc123');

        $this->mockController('/foo', 'foo-controller', function (RequestInterface $request, ResponseInterface $response = null) {
            return new Response();
        });

        $this->dispatch('/foo');

        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        $this->assertHasNotRequestHeader('X-Request-Id');
        $this->assertHasResponseHeader('X-Request-Id');
        $this->assertSame('abc123', $this->getResponseHeader('X-Request-Id')->getFieldValue());
    }

    /**
     * @test
     */
    public function it_return_response_301_with_request_id_from_request_if_controller_return_redirect_response()
    {
        $this->mockGenerator('abc123');

        $_SERVER['HTTP_X_REQUEST_ID'] = 'qwerty987';

        $this->mockController('/foo', 'foo-controller', function (RequestInterface $request, ResponseInterface $response = null) {
            $response->getHeaders()->addHeaderLine('Location', '/bar');
            $response->setStatusCode(302);
            return $response;
        });

        $this->dispatch('/foo');

        $this->assertResponseStatusCode(Response::STATUS_CODE_302);
        $this->assertHasRequestHeader('X-Request-Id');
        $this->assertHasResponseHeader('X-Request-Id');
        $this->assertSame('qwerty987', $this->getResponseHeader('X-Request-Id')->getFieldValue());
    }

    /**
     * @test
     */
    public function it_return_response_404_with_generated_request_id()
    {
        $this->mockGenerator('abc123');

        $this->dispatch('/foo');

        $this->assertResponseStatusCode(Response::STATUS_CODE_404);
        $this->assertHasNotRequestHeader('X-Request-Id');
        $this->assertHasResponseHeader('X-Request-Id');
        $this->assertSame('abc123', $this->getResponseHeader('X-Request-Id')->getFieldValue());
    }

    /**
     * @test
     */
    public function it_return_response_404_with_not_override_request_id()
    {
        $this->mockGenerator('abc123');

        $this->mergeWithConfig([
            'module_listener_options' => [
                'config_glob_paths' => [
                    __DIR__ . "/TestAsset/autoload/not-override-request-id.php",
                ],
            ],
        ]);

        $_SERVER['HTTP_X_REQUEST_ID'] = 'qwerty987';

        $this->dispatch('/foo');

        $this->assertResponseStatusCode(Response::STATUS_CODE_404);
        $this->assertHasRequestHeader('X-Request-Id');
        $this->assertHasResponseHeader('X-Request-Id');
        $this->assertSame('abc123', $this->getResponseHeader('X-Request-Id')->getFieldValue());
    }

    protected function mockGenerator($requestId)
    {
        $generator = $this->getMock(GeneratorInterface::class);
        $generator->method('generateRequestId')->willReturn($requestId);

        $this->mergeWithConfig([
            'service_manager' => [
                'services' => [
                    GeneratorInterface::class => $generator,
                ],
            ],
        ]);
    }

    protected function mergeWithConfig(array $config)
    {
        $appConfig = ArrayUtils::merge($this->getApplicationConfig(), $config);

        $this->setApplicationConfig($appConfig);
    }

    protected function mockController($route, $controllerName, callable $controllerCallback)
    {
        /** @var RouteStackInterface $router */
        $router = $this->getApplicationServiceLocator()->get('Router');
        $router->addRoutes([
            'foo' => [
                'type' => 'literal',
                'options' => [
                    'route' => $route,
                    'defaults' => [
                        'controller' => $controllerName
                    ]
                ]
            ]
        ]);

        $controller = $this->getMock(DispatchableInterface::class);
        $controller->method('dispatch')->willReturnCallback($controllerCallback);

        /** @var ControllerManager $controllerLoader */
        $controllerLoader = $this->getApplicationServiceLocator()->get('ControllerManager');
        $controllerLoader->setService($controllerName, $controller);
    }

    /**
     * Assert request header not exists
     *
     * @param  string $header
     */
    protected function assertHasNotRequestHeader($header)
    {
        $requestHeader = $this->getRegeustHeader($header);

        $this->assertFalse($requestHeader, sprintf(
            'Failed asserting request header "%s" not found',
            $header
        ));
    }


    /**
     * Assert request header exists
     *
     * @param  string $header
     */
    protected function assertHasRequestHeader($header)
    {
        $requestHeader = $this->getRegeustHeader($header);

        $this->assertNotEquals(false, $requestHeader, sprintf(
            'Failed asserting request header "%s" found',
            $header
        ));
    }
    /**
     * Get response header by key
     *
     * @param  string $header
     * @return \Zend\Http\Header\HeaderInterface|false
     */
    protected function getRegeustHeader($header)
    {
        $request = $this->getRequest();
        $headers = $request->getHeaders();
        $requestHeader = $headers->get($header, false);

        return $requestHeader;
    }
}
