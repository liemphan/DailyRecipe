<?php

namespace DailyRecipe\Http;

use Illuminate\Config\Repository;
use Illuminate\Http\Request as LaravelRequest;

class Request extends LaravelRequest
{
    /**
     * Override the default request methods to get the scheme and host
     * to set the custom APP_URL, if set.
     *
     * @return Repository|mixed|string
     */
    public function getSchemeAndHttpHost()
    {
        $base = config('app.url', null);

        if ($base) {
            $base = trim($base, '/');
        } else {
            $base = $this->getScheme() . '://' . $this->getHttpHost();
        }

        return $base;
    }
}
