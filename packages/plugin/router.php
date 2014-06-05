<?php
/**
 * PlgRouter
 *
 * @author      Dave Li <info@kubica.nl>
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
	/**
	 * @param bool $cache
	 */
	public function Router($cache = false)
    {
        $this->_cache	= $cache;
		$this->_lang	= substr(JFactory::getLanguage()->getTag(), 0, 2);
		$this->_routes	= $this->getRoutes();
    }

    /**
     * @param $siteRouter
     * @param $uri
     * @return mixed
     */
    public function build(&$siteRouter, &$uri)
    {
		// TODO: Check if in menu and set query and path accordingly.
		// TODO: Improve!
		$query = $uri->getQuery(true);

		$query2 = array();
		$query2['option'] = $query['option'];
		$query2['view'] = $query['view'];
		$query2['id'] = $query['id'];

		$items = JSite::getMenu()->getItems('link', 'index.php?'.http_build_query($query2), true);

		if($items->id) {
			$merged = array_diff($query, $items->query);
			unset($merged['Itemid']);

			if(is_object($this->_routes->get($query['view']))) {
				preg_match_all('/\{(.*?)\}/ ', $this->_routes->get($query['view'])->getPattern(), $matches);

				$merged = array_diff_key($merged, array_flip($matches[1]));
			}

			$uri->setQuery($merged);
			$uri->setPath($items->route);

			return $uri;
		}

		unset($query['id']);

		$context = new RequestContext('');

		$generator = new UrlGenerator($this->_routes, $context);

		if(array_key_exists($query['view'], $this->_routes->all())) {
			// TODO: Improve!
			if($query['_locale'] && ($query['_locale'] != $this->_lang)) {
				$originalApplicationLanguage = JFactory::getLanguage()->getTag();

				$row = KService::get('com://site/'.str_replace('com_', null, $query['option']).'.model.'.KInflector::pluralize($query['view']))->slug($query['slug'])->getItem();

				JFactory::getLanguage()->setLanguage('fr-FR');

				$row = KService::get('com://site/'.str_replace('com_', null, $query['option']).'.model.'.KInflector::pluralize($query['view']))->id($row->id)->getItem();

				if($row->id) {
					$query['slug'] = $row->slug;
				}

				JFactory::getLanguage()->setLanguage($originalApplicationLanguage);
			}

			$format = $query['format'] ? $query['format'] : 'html';

			$requirements = $this->_routes->get($query['view'])->getRequirements();

			// TODO: Both give unexpected behavior.
//			$query = array_map('strtolower', $query);
//			$query = array_map(array($this , 'sanitize'), $query);

			$config = new KConfig(array_merge($requirements, $query));
			$config->append(array(
				'_locale' => $this->_lang,
				'format' => $format
			));

			try {
				$url = $generator->generate($query['view'], $config->toArray());

				// Remove format since the joomla router handles this.
				$url	= str_replace('.'.$format, null, $url);

				$url	= parse_url($url);
				$path	= $url['path'];
				parse_str($url['query'], $query);

				$uri->setVar('Itemid', $query['Itemid']);
				unset($query['Itemid']);

				$uri->setQuery(array_merge(array('format' => $format), $query));
				$uri->setPath($path);
			} catch (Exception $e) {}
		}

        return $uri;
    }

    /**
     * @param $siteRouter
     * @param $uri
     * @return array
     */
    public function parse(&$siteRouter, &$uri)
    {
		$vars		= array();
		$context	= new RequestContext('/');
		$matcher	= new UrlMatcher($this->_routes, $context);

		try {
			$parameters = $matcher->match('/'.$this->_lang.'/'.$uri->getPath());

			// TODO: Improve!
			if(KInflector::isSingular($parameters['view'])) {
				$query = array('option' => $parameters['option'], 'view' => KInflector::pluralize($parameters['view']));

				$item = JSite::getMenu()->getItems('link', 'index.php?'.http_build_query($query), true);

				if($item->id) {
					JRequest::setVar('Itemid', $item->id);
				}
			}

			$uri->setPath('');
			$uri->setQuery(array_merge($uri->getQuery(true), $parameters));
		} catch (Exception $e) {}

        return $vars;
    }

    /**
     * @return mixed|null
     */
    protected function getRoutes()
    {
		$config = array(JPATH_ADMINISTRATOR.'/components/com_routes/config');
		$locator = new FileLocator($config);
		$loader = new YamlFileLoader($locator);

		try {
			$routes = $loader->load('routing.yml');
		} catch (Exception $e) {
			$routes = new RouteCollection();
		}

        return $routes;
    }

	/**
	 * @param $string
	 * @return mixed
	 */
	public function sanitize($string)
	{
		$filter = KService::get('koowa:filter.slug');

		return $filter->sanitize($string);
	}
}