<?php
// Offline layout fixtures: renders real Blade/CSS/JS with synthetic table rows.
// No HTTP authentication changes and no application records are written.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['cache.default' => 'array', 'session.driver' => 'array', 'app.debug' => false]);
app()->setLocale('en');
Illuminate\Support\Facades\URL::forceRootUrl('http://solent.test');
$user = new App\User(['name' => 'Layout preview', 'first_name' => 'Preview', 'is_admin' => 1]);
$user->id = 1;
Illuminate\Support\Facades\Auth::setUser($user);
$shared = ['errors' => new Illuminate\Support\ViewErrorBag(), 'clients' => collect(), 'cases' => collect(), 'from' => '2026-08-01', 'to' => '2026-08-30', 'currencyContext' => ['display' => 'JOD']];
$stages = [];
foreach (['Design', 'Printing', 'Milling', 'Sintering', 'Pressing', 'MetalWork', 'Finishing', 'QC', 'Delivery'] as $stage) {
    $stages['a'.$stage] = collect();
    $stages['w'.$stage] = collect();
}
$previews = [
    'dashboard' => ['dashboard', ['dashboardSampleDataMode' => true]],
    'invoices' => ['generic.invoices-list', ['invoices' => collect()]],
    'deliveries' => ['delivery.delivery-schedule', ['data' => ['from' => '2026-08-01', 'to' => '2026-08-30']]],
    'cases' => ['cases.index', []],
    'operations' => ['cases.admin-dashboardv2', $stages + ['labs' => collect(), 'devices' => collect(), 'drivers' => collect(), 'employees' => collect(), 'printers' => collect(), 'designers' => collect(), 'millers' => collect(), 'finishingEmployees' => collect(), 'qcEmployees' => collect()]],
];
foreach ($previews as $name => [$view, $data]) {
    try {
        $html = view($view, $data + $shared)->render();
        // Seed layout-only rows before DataTables initializes; these are not saved records.
        $fixtureScript = <<<'HTML'
<script>
document.querySelectorAll('.solent-table-frame table.sunriseTable').forEach(function (table) {
    const headers = Array.from(table.querySelectorAll('thead th'));
    const body = table.tBodies[0] || table.createTBody();
    body.innerHTML = '';
    for (let row = 0; row < 30; row++) {
        const tr = body.insertRow();
        headers.forEach(function (th) {
            const td = tr.insertCell();
            const label = th.textContent.trim().toLowerCase();
            td.textContent = label.includes('doctor') ? 'د. أحمد العجلوني' : label.includes('patient') ? 'ريم النجار / اسم مريض طويل للفحص' : label.includes('amount') ? '125 JOD' : label.includes('date') || label.includes('delivered') ? '2026-08-30 10:30' : label.includes('status') ? 'Waiting in Design' : label.includes('tag') ? 'Normal' : String(1001 + row);
            if (th.querySelector('input')) td.innerHTML = '<input type="checkbox" aria-label="Select preview row">';
        });
    }
});
</script>
HTML;
        // The script runs immediately before the footer starts loading page scripts.
        $html = str_replace('</body>', $fixtureScript.'</body>', $html);
        if (!is_dir(__DIR__.'/mobile-previews')) mkdir(__DIR__.'/mobile-previews', 0777, true);
        file_put_contents(__DIR__.'/mobile-previews/'.$name.'.html', $html);
        echo $name." rendered\n";
    } catch (Throwable $error) {
        echo $name.': '.$error->getMessage()."\n";
    }
}
