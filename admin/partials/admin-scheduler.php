<?php
$currentPage = 'Scheduler';
include RANKFOUNDRY_SEO_PLUGIN_DIR . '/admin/partials/admin-header.php';
?>

<div class="wrap p-8 mt-0 bg-slate-50 rounded shadow-md" x-data="{ activeTab: 'scheduled' }">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold leading-6 text-slate-900">Scheduler</h1>
                <p class="mt-2 text-sm text-slate-700">Manage scheduled events and custom schedules.</p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <button @click="activeTab = 'scheduled'" :class="activeTab === 'scheduled' ? 'bg-slate-800 text-white' : 'bg-slate-300 text-slate-800'" class="mr-2 px-4 py-2 border rounded">Scheduled Events</button>
                <button @click="activeTab = 'custom'" :class="activeTab === 'custom' ? 'bg-slate-800 text-white' : 'bg-slate-300 text-slate-800'" class="px-4 py-2 border rounded">Custom Schedules</button>
            </div>
        </div>
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <div x-show="activeTab === 'scheduled'">
                            <?php
                            $scheduled_events = RankFoundry_SEO_Cron::get_scheduled_events();
                            ?>
                            <table class="min-w-full divide-y divide-slate-300">
                                <thead class="bg-slate-200">
                                    <tr>
                                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 sm:pl-6">Event Name</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Next Run</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Recurring</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    <?php foreach ($scheduled_events as $event): ?>
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-800 sm:pl-6"><?php echo esc_html($event->hook); ?></td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-600"><?php echo get_date_from_gmt(date('Y-m-d H:i:s', $event->timestamp), 'Y-m-d H:i:s'); ?></td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-600"><?php echo $event->schedule ? esc_html($event->schedule) : 'One-time'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div x-show="activeTab === 'custom'">
                            <?php
                            $custom_schedules = RankFoundry_SEO_Cron::get_custom_schedules();
                            ?>
                            <table class="min-w-full divide-y divide-slate-300">
                                <thead class="bg-slate-200">
                                    <tr>
                                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 sm:pl-6">Schedule Name</th>
                                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Interval (seconds)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    <?php foreach ($custom_schedules as $name => $details): ?>
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-800 sm:pl-6"><?php echo esc_html($name); ?></td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-600"><?php echo intval($details['interval']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
