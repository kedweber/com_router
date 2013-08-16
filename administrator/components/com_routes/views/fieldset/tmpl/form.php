<?php
/**
 * Com
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Socialhub
 * @subpackage  ...
 * @uses        Com_
 */

defined('KOOWA') or die('Protected resource');

?>
<fieldset class="panelform">
    <?php $hidden_fields = ''; ?>
    <ul class="adminformlist">
        <?php foreach ($fieldset as $field) : ?>
            <?php if (!$field->hidden) : ?>
                <li>
                    <?php echo $field->label; ?>
                    <?php echo $field->input; ?>
                </li>
            <?php else : $hidden_fields.= $field->input; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <?php echo $hidden_fields; ?>
</fieldset>