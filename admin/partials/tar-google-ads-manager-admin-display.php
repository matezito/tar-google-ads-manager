<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://genosha.com.ar
 * @since      1.0.0
 *
 * @package    Tar_Google_Ads_Manager
 * @subpackage Tar_Google_Ads_Manager/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<style>
    .block-message {
        color: white;
        background: red;
        display: inline-block;
        padding: .3% .6%;
        margin-top: .3%;
        border-radius: 5px;
    }
</style>
<div class="wrap">
    <h1><?php echo __('ADS Manager', 'tar-manager'); ?></h1>
    <p><strong><?php echo __('Info ','tar-manager')?></strong><br /> <?php echo __('Total: ','subscriptions').' '.ADDB()->total_ads()?></p>
    <div class="manager" id="manager-body">
        <?php do_action('manager_messages_header'); ?>
        <div id="ajax-response"></div>
        <div class="manager-list" id="manager-list">
            <?php if (!empty(ADDB()->get_all_ads())) : ?>
                <?php foreach (ADDB()->get_all_ads() as $ad) : ?>
                    <div class="list-item">
                        <form method="post" class="item-form">
                            <h3 class="list-item-header" data-id="<?php echo $ad->ID; ?>"><?php echo $ad->name; ?> <span class="edit"><?php echo __('edit', 'tar-manager'); ?></span></h3>
                            <div class="sizes-list" id="size-<?php echo $ad->ID; ?>">
                                <div class="title-div">
                                    <h4><?php echo __('Ad Title', 'tar-manager'); ?></h4>
                                    <input type="text" name="edit_name" class="regular-text" value="<?php echo $ad->name; ?>" placeholder="<?php echo __('AD Title', 'tar-manager'); ?>" required>
                                    <h4><?php echo __('AD Superior Code', 'tar-manager'); ?></h4>
                                    <input type="text" name="edit_group" class="regular-text" value="<?php echo $ad->group; ?>" placeholder="<?php echo __('AD Superior Code', 'tar-manager'); ?>">
                                    <h4><?php echo __('Ad Code', 'tar-manager'); ?></h4>
                                    <input type="text" name="edit_code" class="regular-text" value="<?php echo $ad->code; ?>" placeholder="<?php echo __('AD Title', 'tar-manager'); ?>" required>
                                </div>
                                <h4><?php echo __('Ad sizes', 'tar-manager'); ?></h4>
                                <?php foreach (ADDB()->get_size_by_ad($ad->ID) as $s) : ?>
                                    <span class="show-size sizes-fields">
                                        <input type="number" name="edit_width[]" value="<?php echo $s->ad_width ?>" class="size-input small-text"> x
                                        <input type="number" name="edit_height[]" value="<?php echo $s->ad_height ?>" class="size-input small-text">
                                        <input type="hidden" name="size_id" value="<?php echo $s->id ?>">
                                        <span class="remove-size" data-id="<?php echo $s->id ?>"><span class="dashicons dashicons-trash"></span></span>
                                    </span>
                                <?php endforeach; ?>
                                <span class="show-size add_new_size" data-id_ad="<?php echo $ad->ID ?>">
                                    <div><span class="dashicons dashicons-plus"></span></div>
                                </span>
                                <hr>
                                <span class="show-size">
                                    <?php echo __('Fluid', 'tar-manager') ?>
                                    <input type="checkbox" name="edit_fluid" value="1" <?php echo $ad->fluid === '1' ? 'checked' : '' ?>>
                                </span>
                                <span class="active-div show-size">
                                    <?php echo __('Active?', 'tar-manager') ?>
                                    <input type="checkbox" name="edit_active" value="1" <?php echo $ad->active === '1' ? 'checked' : '' ?>>
                                </span>
                                <div class="user-info">
                                    <?php $user = get_user_by('ID', $ad->user_id); ?>
                                    <?php echo sprintf(__('Publish by %s at %s', 'tar-manager'), $user->user_nicename, $ad->created_at) ?><br />
                                    <?php echo sprintf(__('Last update: %s ', 'tar-manager'),  $ad->updated_at) ?>
                                </div>
                                <p class="submit">
                                    <input type="hidden" name="ad_user_id" value="<?php echo get_current_user_id() ?>">
                                    <input type="hidden" name="ad_id" value="<?php echo $ad->ID ?>">
                                    <input type="submit" class="button button-primary" name="edit_button" value="<?php echo __('Edit', 'tar-manager') ?>">
                                    <button type="submit" class="button button-secondary delete-button" name="delete-button" data-id="<?php echo $ad->ID ?>"><?php echo __('Delete', 'tar-manager') ?></button>
                                </p>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
                <div class="pagination">
                    <?php ADDB()->show_pagination(); ?>
                </div>
            <?php else : ?>
                <div class="text-center-div">
                    <h3><?php echo __('There is nothing here', 'tar-manager'); ?> </h3>
                </div>
            <?php endif; ?>
        </div>

        <form method="post" id="manager-form-init" class="panel-ads-form">
            <h3><?php echo __('Add new Ad', 'tar-manager'); ?></h3>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label><?php echo __('AD Title *', 'tar-manager'); ?></label></th>
                        <td>
                            <input type="text" name="ad_name" class="regular-text" value="" placeholder="<?php echo __('AD Title', 'tar-manager'); ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo __('AD Superior Code', 'tar-manager'); ?></label></th>
                        <td>
                            <input type="text" name="ad_group" class="regular-text" value="" placeholder="<?php echo __('AD Superior Code', 'tar-manager'); ?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo __('AD Code *', 'tar-manager'); ?></label></th>
                        <td>
                            <input type="text" name="ad_code" class="regular-text" value="" placeholder="<?php echo __('AD Title', 'tar-manager'); ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo __('Fluid?', 'tar-manager'); ?></label></th>
                        <td>
                            <input type="checkbox" name="ad_fluid" value="1">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo __('Active?', 'tar-manager'); ?></label></th>
                        <td>
                            <input type="checkbox" name="ad_active" value="1" checked>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div id="form-new-size">
                <h3 class="add-sizes"><?php echo __('Add sizes', 'tar-manager') ?></h3>
                <table class="form-table" role="presentation" id="table-sizes">
                    <tbody>
                        <tr>
                            <th scope="row"><label><?php echo __('Sizes', 'tar-manager'); ?></label></th>
                            <td>
                                <label class="sizes"><?php echo __('Ad Width', 'tar-manager') ?> <input type="number" name="ad_width[]" class="small-text" style="width:100% !important" value="" placeholder="<?php echo __('Width', 'tar-manager'); ?>"></label>
                                <label for="" class="sizes"><?php echo __('Ad Height', 'tar-manager') ?> <input type="number" name="ad_height[]" class="small-text" style="width:100% !important" value="" placeholder="<?php echo __('Height', 'tar-manager'); ?>"></label>
                                <div class="sizes-row" id="add-row">

                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="submit">
                <input type="hidden" name="ad_user_id" value="<?php echo get_current_user_id() ?>">
                <input type="submit" value="<?php echo __('Add', 'tar-manager') ?>" class="button button-primary" name="add_new_ad">
            </p>
        </form>

        <form method="post" id="csv-manager-form" class="panel-ads-form" enctype="multipart/form-data">
            <h3><?php echo __('Import CSV', 'tar-manager') ?></h3>
            <p><?php echo sprintf(__('Import ads with a csv file. In %s, you have a example of file.', 'tar-manager'),'<a href="'.plugin_dir_url(__DIR__)  . 'ExampleFile.xlsx" target="_blank">'.__('this link','tar-manager').'</a>'); ?> <?php echo __('You must download de file and then save as your-archive-name.csv (csv extension).','tar-manager')?></p>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><?php echo __('Add File', 'tar-manager') ?></th>
                        <td><input type="file" name="import-file" id="import-file" /></td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" value="<?php echo __('Import', 'tar-manager') ?>" class="button button-primary" name="add_new_csv">
            </p>
        </form>

        <form method="post" id="settings-fields" class="panel-ads-form">
            <h3><?php echo __('Google Ad Manager Network Code', 'tar-manager') ?> *</h3>
            <p><?php echo __('Where is it? When you log into your Google Ad Manager account, in the url (for example: https://admanager.google.com/12345678), it is the number after the slash (8 digits)', 'tar-manager'); ?></p>
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><?php echo __('Google Ad Manager Network Code', 'tar-manager') ?></th>
                        <td><input type="text" name="gadmnc" value="<?php echo !empty(get_option('gadmnc')) ? get_option('gadmnc') : ''; ?>" class="regular-text <?php echo !get_option('gadmnc') ? 'error' : '' ?>" /></td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" value="<?php echo __('Add or Edit', 'tar-manager') ?>" class="button button-primary" name="add_nc">
            </p>
        </form>
    </div>
</div>