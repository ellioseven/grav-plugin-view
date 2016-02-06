<?php

namespace Grav\Plugin;

use \Grav\Common\Plugin;
use \Grav\Common\Grav;
use \Grav\Common\Page;
use \Symfony\Component\Yaml\Yaml as YamlParser;

class ViewPlugin extends Plugin
{

    /**
     * Assign listeners
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
            'onGetPageTemplates' => ['onGetPageTemplates', 0],
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
            'onTwigPageVariables' => ['onTwigPageVariables', 0]
        ];
    }

    /**
     * Initialize configuration
     */
    public function onPluginsInitialized()
    {
        $this->active = true;
        return;
    }

    /**
     * Add current directory to twig lookup paths.
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    public function onGetPageTemplates($event)
    {
        $types = $event->types;
        $locator = Grav::instance()['locator'];
        $types->scanBlueprints($locator->findResources('plugin://' . $this->name . '/blueprints'));
        $types->scanTemplates($locator->findResources('plugin://' . $this->name . '/templates'));
    }

    /**
     * Set needed variables to display view.
     *
     * @param $event
     */
    public function onTwigPageVariables($event)
    {
        $page = $event['page'];

        // Get view from page or exit.
        if (isset($page->header()->view)) {
            $view = $page->header()->view;
        } else {
            return;
        }

        // Parse and set params frontmatter.
        if (isset($view['params'])) {
            $view['params'] = (array) YamlParser::parse($view['params']);
        }

        // Set modified header.
        $page->modifyHeader('view', $view);
    }
}