<?php
/**
 * ComRoute
 *
 * @author      Dave Li <dave@moyoweb.nl>
 * @category    Nooku
 * @package     Moyo Components
 * @subpackage  Routes
 */

defined('KOOWA') or die('Protected resource'); ?>

<?= @helper('behavior.bootstrap'); ?>

<?= @helper('behavior.keepalive'); ?>
<?= @helper('behavior.validator'); ?>
<?= @helper('behavior.mootools'); ?>
<?= @helper('behavior.jquery'); ?>
<?= @helper('behavior.modal', array('selector' => 'a.modal-button')); ?>

<script src="media://lib_koowa/js/koowa.js" />

<script>
    jQuery(function($){
        if (typeof Moyo === 'undefined') Moyo = {};

        Moyo.setQuery = function(query) {
            $('#query').val(query);

            SqueezeBox.close();
        };
    });
</script>

<div class="com_routes">
    <form action="" class="form-horizontal -koowa-form" method="post">
        <div class="row-fluid">
            <div class="span8">
                <fieldset>
                    <legend><?= @text('Details'); ?></legend>
                    <div class="control-group">
                        <label class="control-label"><?= @text('Path'); ?></label>
                        <div class="controls">
                            <input class="required" type="text" name="path" value="<?= $route->path ?>" placeholder="<?= @text('Path') ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><?= @text('Query'); ?></label>
                        <div class="controls">
                            <div class="input-append">
                                <?= @helper('modal.select', array(
                                    'name'  => 'query',
                                    'id' => 'query',
                                    'value' => $route->query,
                                    'link'  => @route('index.php?option=com_routes&view=menu&layout=modal&tmpl=component'),
                                    'link_selector' => 'modal-button'
                                ))?>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><?= @text('Itemid'); ?></label>
                        <div class="controls">
                            <div class="input-append">
                                <?= @helper('modal.select', array(
                                    'name'  => 'menu_title',
                                    'id' => 'menu_title',
                                    'target' => 'itemId',
                                    'value' => $route->menu_title,
                                    'link'  => @route('index.php?option=com_routes&view=menus&layout=modal&tmpl=component'),
                                    'link_selector' => 'modal-button'
                                ))?>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="span4">
                <fieldset>
                    <legend><?= @text('Options'); ?></legend>
                    <div class="control-group">
                        <label class="control-label"><?= @text('Enabled'); ?></label>
                        <div class="controls">
                            <?= @helper('select.booleanlist', array('name' => 'enabled', 'selected' => $route->enabled ? $route->enabled : 1)); ?>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <input type="hidden" name="itemId" id="itemId" value="<?= $route->itemId; ?>" />
    </form>
</div>