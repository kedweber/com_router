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

		$this->_cache = $this->params->get('cache');
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

    /**
     * @param bool $cache
     */
    public function Router($cache = false)
    {
        $this->_cache = $cache;
    }

    /**
     * @param $siteRouter
     * @param $uri
     * @return mixed
     */
    public function build(&$siteRouter, &$uri)
    {
		$this->_lang = str_replace('/', '', str_replace('index.php', '', $uri->getPath()));

        $cacheId = 'lang='.$this->_lang.'&'.$uri->getQuery(false);


		if (!empty($this->_cache)) {
            $cachedPathAndQuery = $this->_cache->get('build: '.$cacheId);

            if (!empty($cachedPathAndQuery)) {
                $uri->setPath($cachedPathAndQuery[0]);
                $uri->setQuery($cachedPathAndQuery[1]);
                return $uri;
            }
        }

        $query = $uri->getQuery(true);

//        TODO: Fix!
//        if (empty($matchingRoute)) {
//            $matchingRoute = $this->getMatchingPatternFromQuery($query);
//        }

		$matchingRoute = $this->getMatchingRouteFromQuery($query);

        if (!empty($matchingRoute)) {
			if($matchingRoute['route']->redirect == 1) {
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: ".JUri::root().$matchingRoute['route']->query);
				exit;
			}

			if($matchingRoute['route']->itemId) {
				$app	= JFactory::getApplication();
				$menu   = $app->getMenu();
				$item 	= $menu->getItem($matchingRoute['route']->itemId);

				foreach (array_keys($matchingRoute['route']->query) as $key) {
					unset($query[$key]);
				}

				$uri->setPath($uri->getPath().$item->route);
				$uri->setQuery($query);
			} else {
				$path = $this->getParametrizedPathForMatchingRoute($matchingRoute);
				foreach (array_keys($matchingRoute['route']->query) as $key) {
					unset($query[$key]);
				}

				unset($query['Itemid']);

				$uri->setPath($uri->getPath().$path);
				$uri->setQuery($query);
			}
		}

        if (!empty($this->_cache)) {
            $this->_cache->store(array($uri->getPath(), $uri->getQuery(false)), 'build: '.$cacheId);
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
        $vars = array();

        $cacheId = $uri->getPath();
        if (!empty($this->_cache)) {
            $cachedQueryAndItemId = $this->_cache->get('parse: '.$cacheId);

            if (!empty($cachedQueryAndItemId)) {
                $uri->setPath('');
                $uri->setQuery($cachedQueryAndItemId[0]);
                if ($cachedQueryAndItemId[1]) {
                    JRequest::setVar('Itemid', $cachedQueryAndItemId[1]);
                } else {
                    JRequest::setVar('Itemid', null);
                }
                return $vars;
            }
        }

        $path = $uri->getPath();

        $path = str_replace(JURI::base() . '/', '', $path);
        $path = rtrim($path, '/');
        $matchingRoute = $this->getMatchingRouteFromPath($path);

        if (!empty($matchingRoute)) {

			if($matchingRoute['route']->redirect == 1) {
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: ".JUri::root().$matchingRoute['route']->query);
				exit;
			}

			$newQuery = $this->getParametrizedQueryForMatchingRoute($matchingRoute);
			$oldQuery = $uri->getQuery(false);
			if (!empty($oldQuery)) {
				$newQuery = $newQuery.'&'.$oldQuery;
			}

			$newQuery = preg_replace('#Itemid=[^&]*&#', '', $newQuery);
			$newQuery = preg_replace('#&?Itemid=.*#', '', $newQuery);

			$uri->setPath('');
			$uri->setQuery($newQuery);
			if ($matchingRoute['route']->itemId) {
				JRequest::setVar('Itemid', $matchingRoute['route']->itemId);
			} else {
				JRequest::setVar('Itemid', null);
			}

			if (!empty($this->_cache)) {
				$this->_cache->store(array($uri->getQuery(false), $matchingRoute['route']->itemId), 'parse: '.$cacheId);
			}
		}

        return $vars;
    }

    /**
     * @param $query
     * @return null
     */
    private function getMatchingRouteFromQuery($query) {
        $routes = $this->getRoutes();
        if (empty($routes)) {
            return null;
        }

        $candidateRoutes = array();
        foreach ($routes as $route) {
            $this->switchParameterPatternsFromPathToQuery($route);

            $parameters = array();
            $parameters[] = $route->query;

            $route->query = $this->queryStringToArray($route->query);

            if ($this->checkRouteMatchesQuery($route, $query, $parameters)) {
                $candidateRoute = array();
                $candidateRoute['route'] = $route;
                $candidateRoute['parameters'] = $parameters;

                $candidateRoutes[] = $candidateRoute;
            }
        }

        if (empty($candidateRoutes)) {
            return null;
        }

        return $this->getBestCandidateRouteForQuery($candidateRoutes);
    }

    /**
     * @param $query
     * @return null
     */
    private function getMatchingPatternFromQuery($query)
    {
        $pattern = KService::get('com://admin/routes.model.patterns')->component($query['option'])->view($query['view'])->getItem();

        if($pattern->pattern) {
            $parts = explode('/', $pattern->pattern);

//            foreach($parts as $part) {
//                if(substr($part, 0, 1) == ':') {
//                    $test[] = $query[str_replace(':', '', $part)];
//                } else {
//                    $aww[] = $part;
//                }
//            }

            include_once(JPATH_ROOT . DS . 'components' . DS . $query['option'] . DS . 'router.php');

            //
            $query2 = $query;

            $prefix = str_replace('com_', '', $query['option'] );

            $function = $prefix . 'BuildRoute';

            if(function_exists($function)) {
                $route = $function($query2);
            }

            $path = array_merge($parts, $route);

            $parameters = http_build_query($query);

            $candidateRoute = array();
            $candidateRoute['route'] = (object) array(
                'path'  => implode('/', $path),
                'query' => $query

            );
            $candidateRoute['parameters'] = $parameters;

            if (empty($candidateRoute)) {
                return null;
            }

            return $candidateRoute;
        }
    }

    /**
     * @param $route
     */
    private function switchParameterPatternsFromPathToQuery($route)
    {
        $path = $route->path;
        $query = $route->query;

        if (!preg_match_all('(\([^\)]+\))', $route->path, $parameterPatterns, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            return;
        }

        preg_match_all('(\{\d+\})', $route->query, $parameterPlaceholders, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        $parameterPlaceholderIndexDeltas = array();
        $parameterPatternIndexDeltas = array();
        for ($i=0; $i<count($parameterPlaceholders); $i++) {
            $currentParameterPlaceholderDelta = 0;
            $currentParameterPatternDelta = 0;

            $parameterPlaceholderString = $parameterPlaceholders[$i][0][0];
            $parameterPlaceholderIndex = $parameterPlaceholders[$i][0][1];
            $parameterIndex = (int)substr($parameterPlaceholderString, 1, -1);

            foreach (array_keys($parameterPlaceholderIndexDeltas) as $key) {
                if ($key < $i) {
                    $currentParameterPlaceholderDelta += $parameterPlaceholderIndexDeltas[$key];
                }
            }

            foreach (array_keys($parameterPatternIndexDeltas) as $key) {
                if ($key < $parameterIndex) {
                    $currentParameterPatternDelta += $parameterPatternIndexDeltas[$key];
                }
            }

            $query = substr_replace($query, $parameterPatterns[$parameterIndex-1][0][0], $parameterPlaceholderIndex + $currentParameterPlaceholderDelta, strlen($parameterPlaceholderString));
            $path = substr_replace($path, '{'.($i+1).'}', $parameterPatterns[$parameterIndex-1][0][1] + $currentParameterPatternDelta, strlen($parameterPatterns[$parameterIndex-1][0][0]));

            $parameterPlaceholderIndexDeltas[$i] = strlen($parameterPatterns[$parameterIndex-1][0][0]) - strlen($parameterPlaceholderString);
            $parameterPatternIndexDeltas[$parameterIndex-1] = strlen($parameterPlaceholderString) - strlen($parameterPatterns[$parameterIndex-1][0][0]);
        }

        $route->path = $path;
        $route->query = $query;
    }

    /**
     * @param $stringToEscape
     * @return mixed|string
     */
    private function preparePathOrQueryRegularExpression($stringToEscape)
    {
        if (!preg_match_all('(\([^\)]+\))', $stringToEscape, $parameterPatterns, PREG_SET_ORDER)) {
            return preg_quote($stringToEscape);
        }

        $escapedString = preg_quote($stringToEscape);

        foreach ($parameterPatterns as $parameterPattern) {
            $escapedParameterPattern = preg_quote($parameterPattern[0]);
            $escapedString = str_replace($escapedParameterPattern, $parameterPattern[0], $escapedString);
        }

        return $escapedString;
    }

    /**
     * @param $query
     * @return array
     */
    private function queryStringToArray($query)
    {
        $queryArray = array();

        $queryFields = explode('&', $query);
        foreach ($queryFields as $field) {
            list($key, $value) = explode('=', $field);
            $queryArray[$key] = $value;
        }

        return $queryArray;
    }

    /**
     * @param $route
     * @param $query
     * @param $parameters
     * @return bool
     */
    private function checkRouteMatchesQuery($route, $query, &$parameters)
    {
        foreach ($route->query as $fieldName => $fieldValuePattern) {
            $fieldValuePattern = $this->preparePathOrQueryRegularExpression($fieldValuePattern);

            if (!isset($query[$fieldName]) || !preg_match('#^'.$fieldValuePattern.'$#', $query[$fieldName], $matchedFieldParameters)) {
                return false;
            }

            for ($i=1; $i<count($matchedFieldParameters); $i++) {
                $parameters[] = $matchedFieldParameters[$i];
            }
        }

        return true;
    }

    /**
     * @param $candidateRoutes
     * @return mixed
     */
    private function getBestCandidateRouteForQuery($candidateRoutes)
    {
        $bestRouteIndex = 0;
        $bestRouteFieldCount = count($candidateRoutes[0]['route']->query);
        $bestRouteParameterCount = count($candidateRoutes[0]['parameters']);
        for ($i=1; $i<count($candidateRoutes); $i++) {
            if ((count($candidateRoutes[$i]['route']->query) > $bestRouteFieldCount) ||
                (count($candidateRoutes[$i]['route']->query) == $bestRouteFieldCount &&
                    count($candidateRoutes[$i]['parameters']) < $bestRouteParameterCount)) {
                $bestRouteIndex = $i;
                $bestRouteFieldCount = count($candidateRoutes[$i]['route']->query);
                $bestRouteParameterCount = count($candidateRoutes[$i]['parameters']);
            }
        }

        return $candidateRoutes[$bestRouteIndex];
    }

    /**
     * @param $matchingRoute
     * @return mixed
     */
    private function getParametrizedPathForMatchingRoute($matchingRoute)
    {
        return $this->replaceParameters($matchingRoute['route']->path, $matchingRoute['parameters']);
    }

    /**
     * @param $path
     * @return array|null
     */
    private function getMatchingRouteFromPath($path)
    {
        $routes = $this->getRoutes();
        if (empty($routes)) {
            return null;
        }

        $path = preg_replace('/\.[^.]*$/', '', $path);

        foreach ($routes as $route) {
            $route->path = $this->preparePathOrQueryRegularExpression($route->path);

            if (preg_match('#^'.$route->path.'$#', $path, $parameters)) {

                $matchingRoute = array();
                $matchingRoute['route'] = $route;
                $matchingRoute['parameters'] = $parameters;

                return $matchingRoute;
            }
        }

        return null;

//        $patterns = KService::get('com://admin/routes.model.patterns')->getList();
//
//        foreach($patterns as $pattern) {
//            $parts = explode('/', $pattern->pattern);
//
//            $mofo = explode('/', $path);
//
//            $result = array_intersect($mofo, $parts);
//
//            if(!empty($result)) {
//                $blaat = $pattern;
//            }
//        }
//
//        include_once(JPATH_ROOT . DS . 'components' . DS . $blaat->component . DS . 'router.php');
//
//        $segments = array_filter(explode('/', str_replace($blaat->pattern, '', $path)));
//
//        $prefix = str_replace('com_', '', $blaat->component);
//
//        $function = $prefix . 'ParseRoute';
//
//        $segments = array($blaat->view) + $segments;
//
//        if(function_exists($function)) {
//            $omfg = $function($segments);
//        }
//
//        $matchingRoute = array();
//        $matchingRoute['route'] = (object) array(
//            'path'  => $path,
//            'query' => 'option='.$blaat->component.'&'.http_build_query($omfg)
//        );
//        $matchingRoute['parameters'] = $path;

//        echo "<pre>";
//        print_r($matchingRoute);
//        echo "</pre>";
//        exit;

//        if($matchingRoute) {
//            return $matchingRoute;
//        }

//        return null;
    }

    /**
     * @param $matchingRoute
     * @return mixed
     */
    private function getParametrizedQueryForMatchingRoute($matchingRoute)
    {
        return $this->replaceParameters($matchingRoute['route']->query, $matchingRoute['parameters']);
    }

    /**
     * @param $originalString
     * @param $parameters
     * @return mixed
     */
    private function replaceParameters($originalString, $parameters)
    {
        $replacedString = $originalString;

        foreach ($parameters as $parameterKey => $parameterValue) {
            $replacedString = str_replace('{'.$parameterKey.'}', $parameterValue, $replacedString);
        }

        return $replacedString;
    }

    /**
     * @return mixed|null
     */
    private function getRoutes()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $iso_code = substr(JFactory::getLanguage()->getTag(), 0, 2);

        $query->select('*');
//        if($this->_lang != 'en' && $this->_lang) {
//            $query->from('#__'.$this->_lang.'_routes');
//        } else {
            $query->from('#__routes');
//        }
        $query->where('enabled = 1');
        $query->where('lang IN ('. $db->quote($this->_lang ? $this->_lang : $iso_code).', '.$db->quote('*').')');
        $db->setQuery($query);


        try {
            $result = $db->loadObjectList();
        } catch (DatabaseException $e) {
            return null;
        }

        //Fallback to default table.
        if(count($result) == 0) {
            try {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);

                $query->select('*');
                $query->from('#__routes');
                $query->where('enabled = 1');
				$query->where('lang IN ('. $db->quote('en').', '.$db->quote('*').')');
                $db->setQuery($query);

                $result = $db->loadObjectList();
            } catch (DatabaseException $e) {
                return null;
            }
        }

        return $result;
    }
}