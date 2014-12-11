<?php
/**
 * PlgRouter
 *
 * @author      Joep van der Heijden <joep@moyoweb.nl>
 * @category    Joomla
 * @package     Components
 * @subpackage  Router
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router as SymfonyRouter;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class plgSystemRouter
 */
class plgSystemRouter extends JPlugin
{
	protected $_cache;

	/**
	 * @param $subject
	 * @param $config
	 */
	public function plgSystemRouter(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->_cache = false;
	}

	/**
	 * @return bool
	 */
	public function onAfterInitialise()
	{
		$app = JFactory::getApplication();

		if ($app->isAdmin()) {
			return;
		}

		$router = $app->getRouter();

		$custom_router = new Router($this->getCache());
		$router->attachBuildRule(array($custom_router, 'build'));
		$router->attachParseRule(array($custom_router, 'parse'));

		return true;
	}

	/**
	 * @return JCache|null
	 */
	private function getCache()
	{
		if (!$this->_cache) {
			return null;
		}

		$cache = JFactory::getCache('router', '');
		$cache->setCaching(true);

		return $cache;
	}
}

/**
 * Class Router
 */
class Router
{
	protected $_cache;
	protected $_lang;
	protected $_routes;
    protected $_router;

	/**
	 * @param bool $cache
	 */
	public function Router($cache = false)
	{
		$this->_cache	= $cache;
		$this->_lang	= substr(JFactory::getLanguage()->getTag(), 0, 2);
		$this->_router	= $this->getSymfonyRouter();
		$this->_routes	= $this->_router->getRouteCollection();
	}

	/**
	 * @param $siteRouter
	 * @param $uri
	 * @return mixed
	 */
	public function build(&$siteRouter, &$uri)
	{
		$query = $uri->getQuery(true);

		$query2 = array();
		if (isset($query['option'])) {
			$query2['option'] = $query['option'];
		}
		if (isset($query['view'])) {
			$query2['view'] = $query['view'];
		}
		if (isset($query['id'])) {
			$query2['id'] = $query['id'];
		}

        /**
         * Check if the uri is a menu item.
         */
		$items = JSite::getMenu()->getItems('link', 'index.php?'.urldecode(http_build_query($query2)), true);

		if(is_object($items) && $items->id) {
			$merged = array_diff_key($query, $items->query);
			unset($merged['Itemid']);

			if(is_object($this->_routes->get($query['view']))) {
				preg_match_all('/\{(.*?)\}/ ', $this->_routes->get($query['view'])->getPattern(), $matches);

				$merged = array_diff_key($merged, array_flip($matches[1]));
			}

            $merged['format'] = $query['format'] ? $query['format'] : 'html';

			$uri->setQuery($merged);
			$uri->setPath($items->route);

			return $uri;
		}

        $generator = $this->_router->getGenerator();

		if(array_key_exists($query['view'], $this->_routes->all())) {
			// TODO: Improve!
			// TODO: Check for id.
			if($query['_locale'] && ($query['_locale'] != $this->_lang)) {
				try {
					$originalApplicationLanguage = JFactory::getLanguage()->getTag();

					$row = KService::get('com://site/'.str_replace('com_', null, $query['option']).'.model.'.KInflector::pluralize($query['view']))->slug($query['slug'])->getItem();

					//TODO: Get default languages and select the default one.
					switch ($query['_locale']) {
						case 'en':
							$iso_code = 'en-GB';
							break;
						case 'fr':
							$iso_code = 'fr-FR';
							break;
                        case 'nl':
                            $iso_code = 'nl-NL';
                            break;
						default:
							$iso_code = 'en-GB';
					}

					JFactory::getLanguage()->setLanguage($iso_code);

					$row = KService::get('com://site/'.str_replace('com_', null, $query['option']).'.model.'.KInflector::pluralize($query['view']))->id($row->id)->getItem();

					if($row->id) {
						$query['slug'] = $row->slug;
					}

					JFactory::getLanguage()->setLanguage($originalApplicationLanguage);
				} catch (Exception $e) {}
			}

            $format = isset($query['format']) ? $query['format'] : 'html';

            $requirements = $this->_routes->get($query['view'])->getRequirements();

            $config = new KConfig(array_merge($requirements, $query));
            $config->append(array(
                '_locale' => $this->_lang,
                'format' => $format
            ));

            try {
                if($query['option'] != 'com_search') {
                    $component_router	= $siteRouter->getComponentRouter($query['option']);
                    $vars				= $component_router->build($query);
                }

                if($vars) {
                    foreach($vars as $key => $var) {
                        $config->{$key} = $var;
                    }
                }

                $url = $generator->generate($query['view'], $config->toArray());

                // Remove format since the joomla router handles this.
                $url	= str_replace('.'.$format, null, $url);

                $url	= parse_url($url);
                $path	= $url['path'];
                parse_str($url['query'], $query);

                if(isset($query['Itemid'])) {
                    $uri->setVar('Itemid', $query['Itemid']);
                }
                unset($query['Itemid']);
                unset($query['id']);

                $uri->setQuery(array_merge(array('format' => $format), $query));
                $uri->setPath($path);
            } catch (Exception $e) {}
        }

		$query = array_filter(array_merge($uri->getQuery(true), $query));
		unset($query['_route']);
		unset($query['_locale']);

		$uri->setQuery($query);

		return $uri;
	}

	/**
	 * @param $siteRouter
	 * @param $uri
	 * @return array
	 */
	public function parse(&$siteRouter, &$uri)
	{
		$vars = array();
		$config = new KConfig();

		/**
		 * Check if the path is a menu item
		 */
		$item = JSite::getMenu()->getItems('route', str_replace('.html', '', $uri->getPath()), true);

		if (isset($item->id)) {
			JRequest::setVar('Itemid', $item->id);
		} else {
            /**
             * Path is not a menu item. Resolve path with the Symfony Router.
             */
            try {
                $parameters = $this->_router->match('/' . $this->_lang . '/' . $uri->getPath());
            } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
                // Return a 404
                return $vars;
            } catch (\Symfony\Component\Routing\Exception\MethodNotAllowedException $e) {
                // Return a 405
                // TODO: raise 405.
                return $vars;
            }

            $config->append(array(
                'query' => $parameters
            ));

            /**
             * Check if the route should be redirected
             */
            if($config->query->route && $config->query->permanent) {
                // redirect only on a specific view?
                if ($config->query->view) {
                    if ($config->query->view == $uri->getVar('view')) {
                        $this->redirect($config, $siteRouter, $uri);
                    }
                } else {
                    $this->redirect($config, $siteRouter, $uri);
                }
            }

            /**
             * Get the component router and parse it there.
             */
            if($config->query->option != 'com_search') {
                $component_router	= $siteRouter->getComponentRouter($config->query->option);
                $vars				= $component_router->parse($config->query->toArray());
            }

            $uri->setPath('');
            $uri->setQuery(array_merge($uri->getQuery(true), $config->query->toArray()));
		}

		return $vars;
	}

    protected function redirect($config, $siteRouter, $uri)
    {
        $route = $this->_router->getRouteCollection()->get($config->query->route);

        $config->query->append($route->getDefaults());

        // Override view. View is overriden if it's a redirect for a certain view.
        $config->query->view = $route->getDefault('view');

        $arr = array();
        parse_str($uri->getQuery(), $arr);
        $config->query->append($arr);

        $component_router	= $siteRouter->getComponentRouter($config->query->option);
        $vars				= $component_router->build($config->query->toArray());

        // Add option and view to $vars
        if (!$vars['option']) {
            $vars['option'] = $config->query->option;
        }
        if (!$vars['view']) {
            $vars['view'] = $config->query->view;
        }

        // Build the query and strip empty values in the $vars array.
        $path = http_build_query(array_filter($vars));

        $url = JRoute::_('index.php?' . $path);

        JFactory::getApplication()->redirect($url, true);
        // Request exits after the redirect.
    }

    protected function getSymfonyRouter()
    {
        // Set up the Loader
        $config = array(JPATH_ADMINISTRATOR.'/config/com_routes');
        $locator = new FileLocator($config);
        $loader = new YamlFileLoader($locator);

        // Set up main resource
        $resource = 'routing.yml';

        return new SymfonyRouter($loader, $resource);
    }
}