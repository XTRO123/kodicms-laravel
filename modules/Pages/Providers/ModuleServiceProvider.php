<?php namespace KodiCMS\Pages\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use KodiCMS\CMS\Providers\ServiceProvider;
use KodiCMS\Pages\Behavior\Manager as BehaviorManager;
use KodiCMS\Pages\Helpers\Meta;
use KodiCMS\Pages\Model\Page;
use KodiCMS\Pages\Model\PagePart as PagePartModel;
use KodiCMS\Pages\PagePart;
use KodiCMS\Pages\Observers\PageObserver;
use KodiCMS\Pages\Observers\PagePartObserver;
use Blade;
use Block;
use KodiCMS\Pages\Widget\PagePart as PagePartWidget;

class ModuleServiceProvider extends ServiceProvider {

	public function boot(DispatcherContract $events)
	{
		app('view')->addNamespace('layouts', layouts_path());

		$events->listen('view.page.edit', function ($page)
		{
			echo view('pages::parts.list')->with('page', $page);
		}, 999);

		$events->listen('frontend.found', function($page) {
			app()->singleton('frontpage.meta', function ($app) use ($page)
			{
				return new Meta($page);
			});

			$layoutBlocks = Block::getLayoutBlocks();
			foreach ($layoutBlocks as $block)
			{
				if (!PagePart::exists($page, $block))
				{
					continue;
				}
			}
			//Block::addWidget(new PagePartWidget());
		});

		Blade::extend(function ($view, $compiler)
		{
			$pattern = $compiler->createMatcher('meta');

			return preg_replace($pattern, '$1<?php meta$2; ?>', $view);
		});

		Blade::extend(function ($view, $compiler)
		{
			$pattern = $compiler->createMatcher('block');

			return preg_replace($pattern, '$1<?php Block::run$2; ?>', $view);
		});
	}


	public function register()
	{
		Page::observe(new PageObserver);
		PagePartModel::observe(new PagePartObserver);
		BehaviorManager::init();
	}
}