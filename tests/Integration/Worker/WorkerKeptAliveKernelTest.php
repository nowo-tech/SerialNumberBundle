<?php

declare(strict_types=1);

namespace Nowo\SerialNumberBundle\Tests\Integration\Worker;

use Nowo\SerialNumberBundle\DependencyInjection\NowoSerialNumberExtension;
use Nowo\SerialNumberBundle\Twig\SerialNumberTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\HttpKernel;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

use function gc_collect_cycles;
use function memory_get_usage;
use function sprintf;
use function str_contains;
use function strlen;

/**
 * REQ-RUNTIME-002: ONE container and ONE `HttpKernel`, `services_resetter` / `kernel.reset` never called (FrankenPHP
 * worker, kernel reset false). N iterations alternate two tenants through `HttpKernel::handle()` + `terminate()`:
 * each request renders a Twig template that generates and masks a serial from the tenant prefix and id passed in the
 * request. Every response must contain only its own serial and memory must stay flat.
 */
final class WorkerKeptAliveKernelTest extends TestCase
{
    private const ITERATIONS = 1200;
    private const WARM_UP    = 100;
    private const MARGIN     = 4 * 1024 * 1024;

    public function testAlternatingTenantsInOneKernelLeakNothingAndKeepMemoryFlat(): void
    {
        $container = new ContainerBuilder();
        (new NowoSerialNumberExtension())->load([], $container);
        $container->addCompilerPass(new class implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                $container->getDefinition(SerialNumberTwigExtension::class)->setPublic(true);
            }
        });
        $container->compile(true);

        $twigExtension = $container->get(SerialNumberTwigExtension::class);
        self::assertInstanceOf(SerialNumberTwigExtension::class, $twigExtension);

        $twig = new Environment(new ArrayLoader([
            'serial.txt.twig' => "{% set serial = serial_number({prefix: tenant, year: 2026}, '{prefix}-{year}-{id}', id, 6) %}{{ serial }}|{{ serial|serial_number_mask(4) }}",
        ]));
        $twig->addExtension($twigExtension);

        $kernel  = new HttpKernel(new EventDispatcher(), new ControllerResolver(), new RequestStack(), new ArgumentResolver());
        $handler = static fn (Request $request): Response => new Response($twig->render('serial.txt.twig', [
            'tenant' => $request->attributes->get('tenant'),
            'id'     => $request->attributes->get('id'),
        ]));

        $baseline = 0;
        for ($i = 0; $i < self::ITERATIONS; ++$i) {
            if ($i === self::WARM_UP) {
                gc_collect_cycles();
                $baseline = memory_get_usage();
            }

            [$tenant, $other] = $i % 2 === 0 ? ['FAC', 'ALB'] : ['ALB', 'FAC'];
            $serial           = sprintf('%s-2026-%06d', $tenant, $i);

            $request = Request::create('/serial');
            $request->attributes->add(['_controller' => $handler, 'tenant' => $tenant, 'id' => $i]);
            $response = $kernel->handle($request);
            $content  = (string) $response->getContent();

            self::assertSame(200, $response->getStatusCode(), sprintf('iteration %d', $i));
            self::assertSame($serial . '|' . str_repeat('*', strlen($serial) - 4) . substr($serial, -4), $content, sprintf('iteration %d', $i));
            self::assertFalse(str_contains($content, $other), sprintf('iteration %d: other tenant data present', $i));
            $kernel->terminate($request, $response);
        }

        gc_collect_cycles();
        $growth = memory_get_usage() - $baseline;
        self::assertLessThan(self::MARGIN, $growth, sprintf('memory grew by %d bytes over %d iterations', $growth, self::ITERATIONS - self::WARM_UP));
    }
}
