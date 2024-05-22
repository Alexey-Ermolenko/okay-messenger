<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Psr\Clock\ClockInterface;
use App\Service\RequestLoggerService;
use DateTimeImmutable;
use Doctrine\DBAL\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RawLogProcessSubscriber implements EventSubscriberInterface
{
    private DateTimeImmutable $requestedAt;

    public function __construct(
        private readonly ClockInterface $datetimeService,
        private readonly RequestLoggerService $requestLogger,
    ) {
        $this->requestedAt = $datetimeService->now();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 1048],
            KernelEvents::CONTROLLER => ['onController', -1],
            KernelEvents::TERMINATE => ['onKernelTerminate', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($event->isMainRequest()) {
            $this->requestedAt = $this->datetimeService->now();
        }
    }

    /**
     * @throws Exception
     */
    public function onController(ControllerEvent $event): void
    {
        #if (!$event->isMainRequest()) {
        #    return;
        #}

        #/** @var ReflectionMethod $reflector */
        #$reflector = $event->getControllerReflector();
    }

    /**
     * @throws Exception
     */
    public function onKernelTerminate(TerminateEvent $event): void
    {
        if ($event->isMainRequest()) {
            $this->requestLogger->logRequest(
                $event->getRequest(),
                $event->getResponse(),
                $this->requestedAt,
                $this->datetimeService->now(),
            );
        }
    }
}
