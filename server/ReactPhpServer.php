<?php
namespace TijmenWierenga\Server;

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Response;
use React\Http\Server as HttpServer;
use React\Promise\Promise;
use React\Socket\Server as SocketServer;
use TijmenWierenga\Project\Common\Infrastructure\Bootstrap\App;
use TijmenWierenga\Project\Common\Infrastructure\Ui\Http\RequestHandler;
use TijmenWierenga\Project\Common\Infrastructure\Ui\Http\StreamDataFactory;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
class ReactPhpServer implements Server
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var RequestHandler
     */
    private $requestHandler;

    /**
     * ReactPhpServer constructor.
     * @param Connection $connection
     * @param RequestHandler $requestHandler
     */
    public function __construct(Connection $connection, RequestHandler $requestHandler)
    {
        $this->connection = $connection;
        $this->requestHandler = $requestHandler;
    }

    /**
     * Start a new server
     */
    public function run(): void
    {
        $loop = Factory::create();

        $server = new HttpServer(function (ServerRequestInterface $request) {
            return new Promise(function ($resolve, $reject) use ($request) {
                $request->getBody()->on('data', function ($data) use ($request, &$response) {
                    $response = $this->requestHandler->handle($request, StreamDataFactory::decode($request, $data));
                });

                $request->getBody()->on('end', function () use ($resolve, &$response) {
                    $resolve($response);
                });

                $request->getBody()->on('error', function (\Exception $exception) use ($resolve) {
                    $response = new Response(
                        400,
                        [
                            'Content-Type' => 'text/plain'
                        ],
                        $exception->getMessage()
                    );
                    $resolve($response);
                });
            });
        });

        $socket = new SocketServer((string) $this->connection, $loop);
        $server->listen($socket);

        echo "Server is running on {$this->connection} in environment: " . App::environment();

        $loop->run();
    }
}
