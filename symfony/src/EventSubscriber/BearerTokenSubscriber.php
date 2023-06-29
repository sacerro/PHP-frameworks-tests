<?php

namespace App\EventSubscriber;

use App\Controller\ShortURLController;
use App\Exception\InvalidTokenException;
use App\Exception\MissingTokenException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class BearerTokenSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::EXCEPTION  => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof MissingTokenException || $exception instanceof InvalidTokenException) {
            $response = new JsonResponse([
                'status' => false,
                'error'  => $exception->getMessage(),
            ]);
            $response->setStatusCode(Response::HTTP_FORBIDDEN);

            $event->setResponse($response);
        }
    }

    /**
     * @throws \App\Exception\MissingTokenException
     * @throws \App\Exception\InvalidTokenException
     */
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof ShortURLController) {
            $bearer = preg_replace('/Bearer /i', '', (string)$event->getRequest()->headers->get('authorization'));
            if (empty($bearer)) {
                throw new MissingTokenException('Missing token');
            }
            if (!$this->validateToken($bearer)) {
                throw new InvalidTokenException('Token is not valid');
            }
        }
    }

    private function validateToken(string $token): bool
    {
        if (empty($token) || !preg_match('/[{}()\[\]]{2,}/', $token)) {
            return false;
        }
        $chars = str_split($token);
        $combinations = [
            '(' => ')',
            '{' => '}',
            '[' => ']',
        ];
        $openingChars = array_keys($combinations);
        $closingChars = array_values($combinations);

        $currentOpened = [];

        foreach ($chars as $char) {
            if (in_array($char, $openingChars, true)) {
                $currentOpened[] = $char;
                continue;
            }
            if (in_array($char, $closingChars, true)) {
                // Not opened
                if (empty($currentOpened)) {
                    return false;
                }
                // Check expected closing char
                if ($char !== $combinations[array_pop($currentOpened)]) {
                    return false;
                }
            }
        }
        return empty($currentOpened);
    }
}
