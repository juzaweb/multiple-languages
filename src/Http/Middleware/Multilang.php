<?php

namespace Juzaweb\Multilang\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Juzaweb\CMS\Models\Language;

class Multilang
{
    private UrlGenerator $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next): mixed
    {
        /*$this->url->defaults(
            [
                'locale' => $request->getHost(),
            ]
        );*/

        $type = get_config('mlla_type');
        if ($type == 'session') {
            $locale = $request->get('hl');
            if ($locale) {
                $this->setLocaleSession($locale);
            }
        }

        if ($locale = $this->getLocaleByRequest($request, $type)) {
            App::setLocale($locale);
        }

        view()->share('languages', $this->getSupportLanguages());
        if ($locale) {
            view()->share('language', $locale);
        }

        return $next($request);
    }

    protected function getLocaleByRequest(Request $request, ?string $type)
    {
        if ($type == 'session') {
            // Exclude bots
            if (str_contains(strtolower($request->userAgent()), 'bot')) {
                return false;
            }

            if ($locale = $this->getLocaleSession()) {
                return $locale;
            }

            $acceptLanguage = explode(',', $request->header('accept-language'))[0];
            $acceptLanguage = explode('-', $acceptLanguage)[0];

            if ($acceptLanguage != 'en' && in_array($acceptLanguage, $this->getSupportLanguages())) {
                $this->setLocaleSession($acceptLanguage);
                return $acceptLanguage;
            }

            $this->setLocaleSession($acceptLanguage);
            return $acceptLanguage;
        }

        if ($type == 'subdomain') {
            $domains = get_config('mlla_subdomain');

            if ($domain = Arr::get($domains, $request->getHost())) {
                return $domain['language'];
            }

            return Language::cacheFor(86400)->where(['default' => true])->first()->code;
        }

        return false;
    }

    protected function getLocaleSession()
    {
        $locale = session()->get('jw_locale');
        if ($locale) {
            return $locale;
        }

        $locale = Cookie::get('jw_locale');
        if ($locale) {
            $this->setLocaleSession($locale);
            return $locale;
        }

        return false;
    }

    protected function setLocaleSession($locale): void
    {
        session()->put('jw_locale', $locale);

        Cookie::queue('jw_locale', $locale, time() + 2592000);
    }

    protected function getSupportLanguages(): array
    {
        return Language::cacheFor(3600)->get(['code'])->pluck('code')->toArray();
    }
}
