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
						<label class="control-label"><?= @text('Package'); ?></label>
						<div class="controls">
							<input class="required" type="text" name="package" value="<?= $route->package ?>" placeholder="<?= @text('Package') ?>" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?= @text('Name'); ?></label>
						<div class="controls">
							<input class="required" type="text" name="name" value="<?= $route->name ?>" placeholder="<?= @text('Name') ?>" />
						</div>
					</div>
                    <div class="control-group">
                        <label class="control-label"><?= @text('Path'); ?></label>
                        <div class="controls">
                            <input class="required" type="text" name="path" value="<?= $route->path ?>" placeholder="<?= @text('Path') ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label"><?= @text('Query'); ?></label>
                        <div class="controls">
							<input class="required" type="text" name="query" value="<?= $route->query ?>" placeholder="<?= @text('Query') ?>" />
                        </div>
                    </div>
					<div class="control-group">
						<label class="control-label"><?= @text('Redirect'); ?></label>
						<div class="controls">
							<?= @helper('select.booleanlist', array('name' => 'redirect', 'selected' => $route->redirect)); ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?= @text('Custom'); ?></label>
						<div class="controls">
							<?= @helper('select.booleanlist', array('name' => 'custom', 'selected' => $route->custom)); ?>
						</div>
					</div>
                    <div class="control-group">
						<label class="control-label"><?= @text('ItemId'); ?></label>
						<div class="controls">
							<input type="text" name="itemId" value="<?= $route->itemId ?>" placeholder="<?= @text('ItemId') ?>" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?= @text('Language'); ?></label>
						<div class="controls">
							<input type="text" name="lang" value="<?= $route->lang ?>" placeholder="<?= @text('Language') ?>" />
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