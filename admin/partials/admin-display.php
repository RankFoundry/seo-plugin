<?php
$api_key = get_option('rankfoundry_seo_api_key');
$last_sync = get_option('rankfoundry_seo_last_sync', 'Never');
?>

<div class="wrap">
    <h2>RankFoundry SEO Settings</h2>
    <form method="post" action="options.php">
        <?php settings_fields('rankfoundry_seo'); ?>
        <table class="form-table">
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
