<?php

namespace Grav\Plugin;

use \Grav\Common\Plugin;
use \Grav\Common\Grav;
use \Grav\Common\Page;
use OAuth\Common\Exception\Exception;
use \Symfony\Component\Yaml\Yaml as YamlParser;

class ViewPlugin extends Plugin
{

    /**
     * @var Page
     */
    private $reference;

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

        $params = array();

        // Check for params in page header.
        if (isset($page->header()->view['params'])) {

            // Try to convert Yaml.
            try {
                $params = (array) YamlParser::parse($page->header()->view['params']);
            }

            // Else throw warning.
            catch(\Exception $e) {
                $this->grav['messages']->add('Caught exception: ' .  $e->getMessage() . "\n");
            }
        }

        // Items are needed. Get page children by default.
        if (!isset($params['items'])) {
            $params['items'] = '@self.children';
        }

        return $params;

    }

    /**
     * Get and parse view collection from page header.
     *
     * @param $page
     * @return mixed
     */
    private function getCollection($page) {

        // Get vars.
        $reference = isset($page->header()->view['reference']) ? $page->header()->view['reference'] : '/';
        $params = isset($page->header()->view['params']) ? $page->header()->view['params'] : 'content';
        $filter = isset($page->header()->view['filter']) ? $page->header()->view['filter'] : false;
        $pagination = isset($params['pagination']) ? $params['pagination'] : false;

        // Check if reference root.
        if ($reference !== '/') {
            // Set the reference page, used for filtering.
            $this->reference = $page->find($reference);
            /* @var Collection $collection */
            $collection = $this->reference->collection($params, $pagination);
        } else {
            /* @var Collection $collection */
            $collection = $page->collection($params, $pagination);
        }

        // Filter the page collection.
        if ($collection && $filter) {
            /* @var Collection $collection */
            $collection = $collection->filter(array($this, 'filter'));
        }

        return $collection;

    }

    /**
     * Implements 'onPageProcessed' event.
     * - Sets parent page header pagination to true, enabling the pagination
     * plugin to run for this page.
     *
     * @param $event
     */
    public function onPageProcessed($event) {

        /* @var Page $page */
        $page = $event['page'];

        // If page is a view.
        if ('modular/view' == $page->value('name')) {
            $params = $this->getParams($page);
            if (isset($params['pagination']) && $params['pagination']) {
                $page->parent()->modifyHeader('pagination', true);
            }
        }

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
        $children = $this->reference->children();

        // If key is not in reference page collection, filter it from results.
        if ($children->offsetGet($key)) {
            return true;
        } else {
            return false;
        }

    }

}