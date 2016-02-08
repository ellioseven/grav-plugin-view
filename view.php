<?php

namespace Grav\Plugin;

use \Grav\Common\Plugin;
use \Grav\Common\Grav;
use \Grav\Common\Page;
use \Symfony\Component\Yaml\Yaml as YamlParser;

class ViewPlugin extends Plugin
{

    /**
     * @var Page
     */
    private $target;

    /**
     * Implements 'getSubscribedEvents' event.
     * - Assigns plugin listeners.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Assign listeners.
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            'onGetPageTemplates' => ['onGetPageTemplates', 0],
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
            'onTwigPageVariables' => ['onTwigPageVariables', 0],
            'onPageProcessed' => ['onPageProcessed', 0]
        ];
    }

    /**
     * Implements 'onPluginsInitialized' event.
     * - Set plugin as active.
     */
    public function onPluginsInitialized()
    {
        // Plugin always active.
        $this->active = true;
        return;
    }

    /**
     * Implements 'onTwigTemplatePaths' event.
     * - Add twig paths to instance.
     */
    public function onTwigTemplatePaths()
    {
        // Add current directory to twig lookup paths.
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    /**
     * Implements 'onGetPageTemplates' event.
     * - Add blueprints & templates to instance.
     *
     * @param $event
     */
    public function onGetPageTemplates($event)
    {
        $types = $event->types;

        /* @var Locator $locator */
        $locator = Grav::instance()['locator'];

        // Set blueprints & templates.
        $types->scanBlueprints($locator->findResources('plugin://' . $this->name . '/blueprints'));
        $types->scanTemplates($locator->findResources('plugin://' . $this->name . '/templates'));
    }

    /**
     * Implements 'onTwigPageVariables' event.
     * - Set view vars to page header.
     *
     * @param $event
     */
    public function onTwigPageVariables($event)
    {
        /** @var Page $page */
        $page = $event['page'];

        /** @var Twig $twig */
        $twig = $this->grav['twig'];

        // Exit if no view in page header.
        if (!isset($page->header()->view)) {
            return;
        }

        // Merge config.
        $config = $this->mergeConfig($page);

        // Parse and set params to page header.
        $page->header()->view['params'] = $this->getParams($page);

        // Set twig vars.
        $twig->twig_vars['view']['collection'] = $this->getCollection($page);
        $twig->twig_vars['view']['template'] = $config->get('template');

    }

    /**
     * Get and parse params from page header.
     *
     * @param $page
     * @return array|string
     */
    private function getParams($page) {

        $params = 'content';

        // Check for params in page header.
        if (isset($page->header()->view['params'])) {

            // Convert from Yaml.
            $params = (array) YamlParser::parse($page->header()->view['params']);

            // Items are needed. Get page children by default.
            if (!isset($params['items'])) {
                $params['items'] = '@self.children';
            }
        }

        return $params;

    }

    public function onPageInitialized() {

        // @todo gotta figure this out somehow
        $page = $this->grav['page'];
        $page->modifyHeader('pagination', true);

    }

    public function onCollectionProcessed($event) {

        // @todo gotta figure this out somehow
        $collection = $event['collection'];
        $params = $collection->params();

        if (isset($params['pagination'])) {
            dump($params['pagination']->hasPrev());
        } else {}




    }

    /**
     * Filter view collection result.
     *
     * @param $value
     * @param $key
     * @return bool
     */
    public function filter($value, $key) {

        /* @var Collection $children */
        $children = $this->target->children();

        // If key is not in target page collection, filter it from results.
        if ($children->offsetGet($key)) {
            return true;
        } else {
            return false;
        }

    }

}