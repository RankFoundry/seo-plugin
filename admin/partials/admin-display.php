<?php
$api_key = get_option('rankfoundry_seo_api_key');
$last_sync = get_option('rankfoundry_seo_last_sync', 'Never');
$sync_activation = get_option('rankfoundry_seo_sync_activation', '0');
?>

<div class="wrap">
    <h1>RankFoundry SEO Settings</h1>
    <form method="post" action="options.php">
        <?php settings_fields($this->plugin_name); ?>

        <h2>SEO Command Center Sync</h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Enable Sync</th>
                <td>
                    <label>
                        <input type="checkbox" name="rankfoundry_seo_sync_activation" <?php checked($sync_activation, '1'); ?> />
                        Check this box to enable synchronization with the SEO Command Center.
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">API Key</th>
                <td>
                    <input type="text" name="rankfoundry_seo_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text" />
                    <p class="description">Enter your SEO Command Center API key here.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Last Sync</th>
                <td>
                    <?php echo $last_sync; ?>
                    <p class="description">This is the last time data was synchronized.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Manual Sync</th>
                <td>
                    <button type="button" id="manual-sync" class="button button-secondary">Sync Now</button>
                    <p class="description">Manually trigger a data synchronization.</p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <?php submit_button('Save Changes', 'primary', 'submit', false); ?>
        </p>
    </form>
</div>


<script>
    document.querySelector('[name="rankfoundry_seo_sync_activation"]').addEventListener('change', function() {
        let action = this.checked ? 'activate_sync' : 'deactivate_sync';

        if ((this.checked && confirm("Are you sure you want to activate the sync?")) || 
            (!this.checked && confirm("Are you sure you want to deactivate the sync?"))) {
            fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=' + action,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                } else {
                    alert('Operation failed! ' + (data.message || 'Unknown error.'));
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
