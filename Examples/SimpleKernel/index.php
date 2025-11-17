<?php

declare(strict_types=1);

use Amp\ByteStream;
use Amp\Http\Server\DefaultErrorHandler;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\SocketHttpServer;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ContainerControllerResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterControllerArgumentLocatorsPass;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Examples\SimpleKernel\AttributeRouteControllerLoader;
use Symfony\Component\HttpKernel\Examples\SimpleKernel\Controller\HelloController;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\Routing\Matcher\RedirectableUrlMatcher;
use Symfony\Component\Routing\Loader\AttributeDirectoryLoader;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

require __DIR__ . '/../../vendor/autoload.php';

// Note any PSR-3 logger may be used, Monolog is only an example.
$logHandler = new StreamHandler(ByteStream\getStdout());
$logHandler->pushProcessor(new PsrLogMessageProcessor());
$logHandler->setFormatter(new ConsoleFormatter());

$logger = new Logger('server');
$logger->pushHandler($logHandler);

// LOCATE THE ROUTES
$locator = new FileLocator(__DIR__ . '/Controller');
$attrLoader = new AttributeRouteControllerLoader();
$loader = new AttributeDirectoryLoader($locator, $attrLoader);

// GET ROUTES
$routes = $loader->load(__DIR__ . '/Controller');

//dump($routes); die;

// URL MATCHER
//$matcher = new UrlMatcher($routes, new RequestContext());
//$matcher = new RedirectableUrlMatcher()
//

//$baseMatcher = new Symfony\Component\Routing\Matcher\UrlMatcher($routes, new RequestContext());
$matcher = new RedirectableUrlMatcher($routes, new RequestContext());

// Prepare the dispatcher
$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher, logger: $logger));

// Prepare the container
$container = new ContainerBuilder();
$container->set(HelloController::class, new HelloController());
$container->set(LoggerInterface::class, $logger);
$container->addCompilerPass(new RegisterControllerArgumentLocatorsPass());
$container->compile();


$serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
$controllerResolver = new ContainerControllerResolver($container, $logger);

$requestPayloadResolver = new ArgumentResolver\RequestPayloadValueResolver(
    $serializer
);
$dispatcher->addSubscriber($requestPayloadResolver);


$argumentResolver = new ArgumentResolver(
    argumentMetadataFactory: new ArgumentMetadataFactory(),
    argumentValueResolvers: [
        $requestPayloadResolver,
        new ArgumentResolver\QueryParameterValueResolver(),
        ...ArgumentResolver::getDefaultArgumentValueResolvers()
    ]
);

$kernel = new HttpKernel(
    $dispatcher,
    $controllerResolver,
    $argumentResolver,
    handleAllThrowables: false
);

final readonly class KernelHandler implements RequestHandler
{
    public function __construct(
        private HttpKernel $kernel,
    )
    {
    }

    public function handleRequest(Request $request): Response
    {
        return $this->kernel->handle($request);
    }
}

$kernelHandler = new KernelHandler($kernel);

$errorHandler = new DefaultErrorHandler();

$server = SocketHttpServer::createForDirectAccess($logger);
$server->expose('127.0.0.1:1337');
$server->start($kernelHandler, $errorHandler);

// Serve requests until SIGINT or SIGTERM is received by the process.
Amp\trapSignal([SIGINT, SIGTERM]);

$server->stop();