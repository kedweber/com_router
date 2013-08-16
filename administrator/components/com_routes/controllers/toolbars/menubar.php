<?php

/**
 * Joomla handles the menubar for us from the manifest.xml
 *
 */
class ComRoutesControllerToolbarMenubar extends ComDefaultControllerToolbarMenubar
{
    public function getCommands()
    {
        return parent::getCommands();
    }
}