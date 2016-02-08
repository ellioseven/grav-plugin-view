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
        // Get variables.
        $page = $event['page'];
        $config = $this->mergeConfig($page);
        $header = $page->header();

        // Exit if no view in page header.
        if (!isset($header->view)) {
            return;
        }

        // Define view vars.
        $view_header['template'] = $config['template'];
        $view_header['items'] = $this->getItems($page);
        $view_header['view_url'] = $page->parent()->url();

        // Set page header.
        $page->modifyHeader('view', $view_header);
    }

    /**
     * Get view items from page collection.
     *
     * @param $page
     * @return mixed
     */
    private function getItems($page) {

        // Get vars.
        $view_header = $page->header()->view;
        $params = isset($view_header['params']) ? $view_header['params'] : false;
        $filter = isset($view_header['limit']) ? $view_header['limit'] : false;
        $pagination = isset($view_header['pagination']) ? $view_header['pagination'] : false;

        // Parse params or set to default.
        if ($params) {
            $params = (array) YamlParser::parse($params);
        } else {
            $params = 'content';
        }

        // Check if page root.
        if ($view_header['page'] != '/') {

            // Set the target page.
            $this->target = $page->find($view_header['page']);

            // Get the target page collection.
            $items = $this->target->collection($params, $pagination);

            // Filter the page collection.
            if ($items && $filter) {
                $items = $items->filter(array($this, 'filter'));
            }

        } else {

            // Get the page collection.
            $items = $page->collection($params, $pagination);

        }

        return $items;

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

        // If key is not in target page collection, filter it from results.
        $children = $this->target->children();
        if ($children->offsetGet($key)) {
            return true;
        } else {
            return false;
        }

    }

}