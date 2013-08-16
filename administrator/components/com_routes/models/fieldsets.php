<?php
/**
 * ComRoutes
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Moyo Components
 * @subpackage  Routes
 */

defined('KOOWA') or die('Protected resource');

class ComRoutesModelFieldsets extends ComDefaultModelDefault
{
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        $this->_state
            ->insert('componentname'   , 'string', 'com_content')
            ->insert('componentview'     , 'string', 'article')
            ->insert('fieldset' , 'string', 'request')
        ;
    }

    public function getRow(array $options = array())
    {
        $identifier         = clone $this->getIdentifier();
        $identifier->path   = array('database', 'row');
        $identifier->name   = KInflector::singularize($this->getIdentifier()->name);

        return $this->getService($identifier, $options);
    }

    public function getItem()
    {
        $this->_item = $this->getRow()->setData($this->getForm()->getFieldset($this->_state->fieldset));

        return $this->_item;
    }

    public function getForm($data, $group = 'content')
    {
        $state = $this->_state;

        $form = new JForm();

        $formFile = false;

        $link = htmlspecialchars_decode($link);

        // Parse the link arguments.
        $args = array();
        parse_str(parse_url(htmlspecialchars_decode($link), PHP_URL_QUERY), $args);

        // Confirm that the option is defined.
        $option = '';
        $base = '';
        $args['option'] = $state->componentname;
        $args['view'] = $state->componentview;
        //$args['layout'] = 'blog';

        if (isset($args['option'])) {
            // The option determines the base path to work with.
            $option = $args['option'];
            $base	= JPATH_SITE.'/components/'.$option;
        }

        // Confirm a view is defined.
        $formFile = false;
        if (isset($args['view'])) {
            $view = $args['view'];

            // Determine the layout to search for.
            if (isset($args['layout'])) {
                $layout = $args['layout'];
            }
            else {
                $layout = 'default';
            }

            $formFile = false;

            // Check for the layout XML file. Use standard xml file if it exists.
            $path = JPath::clean($base.'/views/'.$view.'/tmpl/'.$layout.'.xml');
            if (JFile::exists($path)) {
                $formFile = $path;
            }

            // if custom layout, get the xml file from the template folder
            // template folder is first part of file name -- template:folder
            if (!$formFile && (strpos($layout, ':') > 0 ))
            {
                $temp = explode(':', $layout);
                $templatePath = JPATH::clean(JPATH_SITE.'/templates/'.$temp[0].'/html/'.$option.'/'.$view.'/'.$temp[1].'.xml');
                if (JFile::exists($templatePath))
                {
                    $formFile = $templatePath;
                }
            }
        }

        //Now check for a view manifest file
        if (!$formFile)
        {
            if (isset($view) && JFile::exists($path = JPath::clean($base.'/views/'.$view.'/metadata.xml')))
            {
                $formFile = $path;
            }
            else
            {
                //Now check for a component manifest file
                $path = JPath::clean($base.'/metadata.xml');
                if (JFile::exists($path))
                {
                    $formFile = $path;
                }
            }
        }


        if ($formFile) {
            // If an XML file was found in the component, load it first.
            // We need to qualify the full path to avoid collisions with component file names.

            if ($form->loadFile($formFile, true, '/metadata') == false) {
                throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
            }

            // Attempt to load the xml file.
            if (!$xml = simplexml_load_file($formFile)) {
                throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
            }

            // Get the help data from the XML file if present.
            $help = $xml->xpath('/metadata/layout/help');
            if (!empty($help)) {
                $helpKey = trim((string) $help[0]['key']);
                $helpURL = trim((string) $help[0]['url']);
                $helpLoc = trim((string) $help[0]['local']);

                $this->helpKey = $helpKey ? $helpKey : $this->helpKey;
                $this->helpURL = $helpURL ? $helpURL : $this->helpURL;
                $this->helpLocal = (($helpLoc == 'true') || ($helpLoc == '1') || ($helpLoc == 'local')) ? true : false;
            }

        }

        // Now load the component params.
        // TODO: Work out why 'fixing' this breaks JForm
        if ($isNew = false) {
            $path = JPath::clean(JPATH_ADMINISTRATOR.'/components/'.$option.'/config.xml');
        }
        else {
            $path='null';
        }

        if (JFile::exists($path)) {
            // Add the component params last of all to the existing form.
            if (!$form->load($path, true, '/config')) {
                throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
            }
        }

//        echo "<pre>";
//        print_r($form);
//        echo "</pre>";
//        exit;

        // Load the specific type file
//        if (!$form->loadFile('item_'.$type, false, false)) {
//            throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
//        }

        // Association menu items
//        if (JFactory::getApplication()->get('menu_associations', 0)) {
//            $languages = JLanguageHelper::getLanguages('lang_code');
//
//            $addform = new JXMLElement('<form />');
//            $fields = $addform->addChild('fields');
//            $fields->addAttribute('name', 'associations');
//            $fieldset = $fields->addChild('fieldset');
//            $fieldset->addAttribute('name', 'item_associations');
//            $fieldset->addAttribute('description', 'COM_MENUS_ITEM_ASSOCIATIONS_FIELDSET_DESC');
//            $add = false;
//            foreach ($languages as $tag => $language)
//            {
//                if ($tag != $data['language']) {
//                    $add = true;
//                    $field = $fieldset->addChild('field');
//                    $field->addAttribute('name', $tag);
//                    $field->addAttribute('type', 'menuitem');
//                    $field->addAttribute('language', $tag);
//                    $field->addAttribute('label', $language->title);
//                    $field->addAttribute('translate_label', 'false');
//                    $option = $field->addChild('option', 'COM_MENUS_ITEM_FIELD_ASSOCIATION_NO_VALUE');
//                    $option->addAttribute('value', '');
//                }
//            }
//            if ($add) {
//                $form->load($addform, false);
//            }
//        }

        return $form;
    }

    public function __get($column)
    {
        if($column == 'request') {
            die('test');
            return $this->getForm()->getFieldset('request');
        }

        return parent::__get($column);
    }
}