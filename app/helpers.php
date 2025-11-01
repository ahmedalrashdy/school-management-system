<?php

use App\Services\LookupService;
use App\Services\SchoolContextService;

if (! function_exists('school')) {
    /**
     * Get the school context service instance.
     */
    function school(): SchoolContextService
    {
        return app(SchoolContextService::class);
    }
}

if (! function_exists('lookup')) {
    function lookup()
    {
        return app(LookupService::class);
    }
}
