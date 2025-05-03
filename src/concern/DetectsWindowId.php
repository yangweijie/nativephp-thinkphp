<?php

namespace native\thinkphp\concern;


trait DetectsWindowId
{
    protected function detectId(): ?string
    {
        $previousUrl = request()->headers->get('Referer');
        $currentUrl = request()->url(true);

        // Return the _windowId query parameter from either the previous or current URL.
        $parsedUrl = parse_url($previousUrl ?? $currentUrl);
        parse_str($parsedUrl['query'] ?? '', $query);

        return $query['_windowId'] ?? null;
    }
}
