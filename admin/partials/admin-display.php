<?php
$api_key = get_option('rankfoundry_seo_api_key');
$last_sync = get_option('rankfoundry_seo_last_sync', 'Never');
$sync_activation = get_option('rankfoundry_seo_sync_activation', 'off');
?>

<div class="wrap">
    <h2>RankFoundry SEO Settings</h2>
    <form method="post" action="options.php">
        <?php settings_fields($this->plugin_name); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">SEO Command Center Sync</th>
                <td>
                    <input type="checkbox" name="rankfoundry_seo_sync_activation" value="on" <?php checked($sync_activation, 'on'); ?> />
                    Enable Sync
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">SEO Command Center API Key</th>
                <td><input type="text" name="rankfoundry_seo_api_key" value="<?php echo esc_attr($api_key); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Last Sync</th>
                <td><?php echo $last_sync; ?></td>
            </tr>
            <tr valign="top">
                <th scope="row">Manual Sync</th>
                <td><button type="button" id="manual-sync" class="button button-secondary">Sync Now</button></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>

<script>
    // Add a nonce for security
    let nonce = '<?php echo wp_create_nonce("rankfoundry_seo_sync_activation_nonce"); ?>';

    document.querySelector('[name="rankfoundry_seo_sync_activation"]').addEventListener('change', function() {
        let action = this.checked ? 'activate_sync' : 'deactivate_sync';

        if ((this.checked && confirm("Are you sure you want to activate the sync?")) || 
            (!this.checked && confirm("Are you sure you want to deactivate the sync?"))) {
            fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=' + action + '&nonce=' + nonce,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                } else {
                    alert('Operation failed!');
                    // Toggle the checkbox back to its original state if the operation failed
                    this.checked = !this.checked;
                }
            });
        } else {
            // Revert the checkbox to its original state if the user cancels the confirmation
            this.checked = !this.checked;
        }
    });

    document.getElementById('manual-sync').addEventListener('click', function() {
        fetch(ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=manual_sync',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Optionally, you can update the Last Sync time on the page here.
            } else {
                alert('Sync failed!');
            }
        });
    });
</script>
