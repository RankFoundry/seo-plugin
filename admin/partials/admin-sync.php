<?php
$currentPage = 'Sync';
include RANKFOUNDRY_SEO_PLUGIN_DIR . '/admin/partials/admin-header.php';

$api_key = get_option('rankfoundry_seo_api_key');
$last_sync = get_option('rankfoundry_seo_last_sync', 'Never');
$sync_activation = get_option('rankfoundry_seo_sync_activation', '0');
?>

<div class="wrap p-8 mt-0 bg-slate-50 rounded shadow-md">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center mb-6">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold leading-6 text-slate-900">SEO Command Center Sync Settings</h1>
                <p class="mt-2 text-sm leading-6 text-slate-700">Configure synchronization settings and API keys.</p>
            </div>
        </div>
        
        <div class="overflow-hidden bg-white shadow sm:rounded-lg">
            <form method="post" action="options.php">
                <?php settings_fields($this->plugin_name); ?>

                <dl class="divide-y divide-slate-100">
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-slate-900">
                            Synchronization Setting
                            <p class="mt-2 text-sm leading-6 text-slate-400">Toggle this setting to enable or disable the synchronization between your website and the SEO Command Center.</p>
                        </dt>
                        <dd class="mt-1 text-sm leading-6 text-slate-700 sm:col-span-2 sm:mt-0">
                            <input type="checkbox" name="rankfoundry_seo_sync_activation" <?php checked($sync_activation, '1'); ?> class="form-checkbox h-4 w-4 text-slate-600 rounded">
                        </dd>
                    </div>

                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-slate-900">
                            SEO Command Center API Key
                            <p class="mt-2 text-sm leading-6 text-slate-400">Input your unique API key to establish a connection with the SEO Command Center. Ensure the key is accurate to enable seamless data synchronization.</p>
                        </dt>
                        <dd class="mt-1 flex items-center text-sm leading-6 text-slate-700 sm:col-span-2 sm:mt-0">
                            <input type="text" name="rankfoundry_seo_api_key" value="<?php echo esc_attr($api_key); ?>" id="api_key" class="h-10 p-2 border rounded w-1/2 mr-4">
                            <button type="submit" class="h-10 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-slate-600 hover:bg-slate-700 focus:outline-none focus:border-slate-700 focus:ring focus:ring-slate-200 active:bg-slate-800">Save Changes</button>
                        </dd>
                    </div>

                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-slate-900">
                            Last Synchronization
                            <p class="mt-2 text-sm leading-6 text-slate-400">The most recent instance when the data was successfully synchronized with the SEO Command Center is displayed here.</p>
                        </dt>
                        <dd class="mt-1 text-sm leading-6 text-slate-700 sm:col-span-2 sm:mt-0" id="last_sync_time"><?php echo $last_sync; ?></dd>
                    </div>

                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-slate-900">
                            Manual Sync
                            <p class="mt-2 text-sm leading-6 text-slate-400">Initiate an immediate synchronization with the SEO Command Center.</p>
                        </dt>
                        <dd class="mt-1 text-sm leading-6 text-slate-700 sm:col-span-2 sm:mt-0">
                            <button type="button" id="manual-sync" class="mt-2 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-slate-600 hover:bg-slate-700 focus:outline-none focus:border-slate-700 focus:ring focus:ring-slate-200 active:bg-slate-800">Sync Now</button>
                        </dd>
                    </div>
                </dl>
            </form>
        </div>
    </div>
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

    document.addEventListener('DOMContentLoaded', function() {
        // Function to fetch and update the last sync time
        function updateLastSync() {
            fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get_last_sync',
            })
            .then(response => response.text())
            .then(data => {
                document.querySelector('#last_sync_time').textContent = data;
            });
        }

        // Update the last sync time every 60 seconds (60000 milliseconds)
        setInterval(updateLastSync, 60000);
    });
</script>
