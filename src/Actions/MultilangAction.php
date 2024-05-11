<?php

namespace Juzaweb\Multilang\Actions;

use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;

class MultilangAction extends Action
{
    public function handle(): void
    {
        $this->addAction(Action::BACKEND_INIT, [$this, 'adminActions']);
        $this->addAction(Action::INIT_ACTION, [$this, 'addConfigs']);
    }

    public function adminActions(): void
    {
        HookAction::registerAdminPage(
            'multilingual',
            [
                'title' => trans('cms::app.multilingual'),
                'menu' => [
                    'position' => 30,
                    'parent' => 'setting',
                ]
            ]
        );

        HookAction::registerAdminPage(
            'languages',
            [
                'title' => trans('cms::app.languages'),
                'menu' => [
                    'position' => 30,
                    'parent' => 'managements',
                ]
            ]
        );

        $this->enqueueScript(
            'mlla',
            plugin_asset('js/select-language.min.js', 'juzaweb/multiple-languages'),
            '1.0',
            true
        );
    }

    public function addConfigs(): void
    {
        HookAction::registerConfig(['mlla_type', 'mlla_subdomain']);
    }
}
