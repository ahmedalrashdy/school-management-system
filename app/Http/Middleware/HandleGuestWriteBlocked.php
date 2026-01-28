<?php

namespace App\Http\Middleware;

use App\Support\GuestWriteBlocker;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleGuestWriteBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $blocker = app(GuestWriteBlocker::class);

        if (! $blocker->isBlocked()) {
            return $response;
        }

        if ($this->isLivewireRequest($request)) {
            return $this->attachToastToLivewireResponse($response, $blocker->message());
        }

        return redirect()
            ->back()
            ->withInput()
            ->with('info', $blocker->message());
    }

    private function isLivewireRequest(Request $request): bool
    {
        return $request->hasHeader('X-Livewire');
    }

    private function attachToastToLivewireResponse(Response $response, string $message): Response
    {
        $payload = null;

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $payload = $response->getData(true);
        } else {
            $content = $response->getContent();
            $payload = is_string($content) ? json_decode($content, true) : null;
        }

        if (! is_array($payload) || ! isset($payload['components']) || ! is_array($payload['components'])) {
            return $response;
        }

        $dispatch = [
            'name' => 'show-toast',
            'params' => [
                'type' => 'info',
                'message' => $message,
            ],
        ];

        $patched = false;
        foreach ($payload['components'] as &$component) {
            if (! is_array($component)) {
                continue;
            }

            $effects = $component['effects'] ?? [];
            $effects['dispatches'] = array_values(array_merge($effects['dispatches'] ?? [], [$dispatch]));
            $component['effects'] = $effects;
            $patched = true;
            break;
        }
        unset($component);

        if (! $patched) {
            return $response;
        }

        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $response->setData($payload);
        } else {
            $response->setContent(json_encode($payload, JSON_UNESCAPED_UNICODE));
        }

        return $response;
    }
}
