<?php
// Ensure currentPage is set; if not, default to 'Dashboard'
if(!isset($currentPage)) {
    $currentPage = 'Dashboard';
}

// Determine the active menu based on currentPage
$activeMenu = ['Dashboard' => '', 'Integration' => '', 'Scheduler' => ''];
if(isset($activeMenu[$currentPage])) {
    $activeMenu[$currentPage] = 'bg-slate-50 text-slate-900 rounded-t-md px-3 py-2';
}
?>

<!-- Breadcrumb -->
<div class="wrap bg-slate-50 rounded p-2">
    <nav class="pl-6 text-sm font-medium text-slate-500" aria-label="Breadcrumb">
        <a href="<?php echo menu_page_url('rankfoundry-seo', false); ?>" class="hover:text-slate-700">Home</a>
        <span class="mx-2 text-slate-400">/</span>
        <span class="text-slate-700" aria-current="page"><?php echo $currentPage; ?></span>
    </nav>
</div>

<!-- Logo and Brand Name -->
<div class="flex items-center p-4">
    <img src="<?php echo plugins_url('../../assets/images/rank-foundry-logo.png', __FILE__); ?>" alt="Rank Foundry Logo" class="w-16">
    <h1 class="ml-4 text-2xl font-semibold text-slate-900">Rank Foundry SEO</h1>
</div>


<!-- Navigation -->
<div class="mb-0">
    
    <!-- Desktop Version -->
    <div class="hidden sm:block">
        <div class="pl-8">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="<?php echo menu_page_url('rankfoundry-seo', false); ?>" class="<?php echo $activeMenu['Dashboard']; ?> border-transparent text-slate-500 hover:text-slate-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">Dashboard</a>
                <a href="<?php echo menu_page_url('rankfoundry-seo-integration', false); ?>" class="<?php echo $activeMenu['Integration']; ?> border-transparent text-slate-500 hover:text-slate-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">Integration</a>
                <a href="<?php echo menu_page_url('rankfoundry-seo-scheduler', false); ?>" class="<?php echo $activeMenu['Scheduler']; ?> border-transparent text-slate-500 hover:text-slate-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">Scheduler</a>
            </nav>
        </div>
    </div>
</div>