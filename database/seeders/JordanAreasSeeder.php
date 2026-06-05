<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JordanAreasSeeder extends Seeder
{
    private array $areas = [
        ['name' => "Abdoun", 'city' => "Amman", 'latitude' => 31.950000, 'longitude' => 35.883000],
        ['name' => "Shmeisani", 'city' => "Amman", 'latitude' => 31.967000, 'longitude' => 35.917000],
        ['name' => "Jabal Amman", 'city' => "Amman", 'latitude' => 31.950560, 'longitude' => 35.923060],
        ['name' => "Jubeiha", 'city' => "Amman", 'latitude' => 32.020560, 'longitude' => 35.895000],
        ['name' => "Marj Al-Hamam", 'city' => "Amman", 'latitude' => 31.902861, 'longitude' => 35.846444],
        ['name' => "Al-Muqabalayn", 'city' => "Amman", 'latitude' => 31.900000, 'longitude' => 35.900000],
        ['name' => "Wadi Al-Seer", 'city' => "Amman", 'latitude' => 31.940000, 'longitude' => 35.850000],
        ['name' => "Sweifieh", 'city' => "Amman", 'latitude' => 31.955000, 'longitude' => 35.870000],
        ['name' => "Al-Wehdat", 'city' => "Amman", 'latitude' => 31.940000, 'longitude' => 35.940000],
        ['name' => "Ras Al-Ain", 'city' => "Amman", 'latitude' => 31.960000, 'longitude' => 35.935000],
        ['name' => "Tabarbour", 'city' => "Amman", 'latitude' => 32.010000, 'longitude' => 35.965000],
        ['name' => "Marka", 'city' => "Amman", 'latitude' => 31.980000, 'longitude' => 35.990000],
        ['name' => "Abu Nsair", 'city' => "Amman", 'latitude' => 32.030000, 'longitude' => 35.900000],
        ['name' => "Khalda", 'city' => "Amman", 'latitude' => 31.990000, 'longitude' => 35.850000],
        ['name' => "Al-Bayader", 'city' => "Amman", 'latitude' => 31.920000, 'longitude' => 35.870000],
        ['name' => "Naour", 'city' => "Amman", 'latitude' => 31.870000, 'longitude' => 35.830000],
        ['name' => "Sahab", 'city' => "Amman", 'latitude' => 31.860000, 'longitude' => 36.000000],
        ['name' => "Al-Jizah", 'city' => "Amman", 'latitude' => 31.830000, 'longitude' => 35.930000],
        ['name' => "Jabal Tariq", 'city' => "Zarqa", 'latitude' => 32.083000, 'longitude' => 36.100000],
        ['name' => "New Zarqa", 'city' => "Zarqa", 'latitude' => 32.073000, 'longitude' => 36.087000],
        ['name' => "Al-Hashimiyah", 'city' => "Zarqa", 'latitude' => 32.138500, 'longitude' => 36.109600],
        ['name' => "Russeifa", 'city' => "Zarqa", 'latitude' => 32.020000, 'longitude' => 36.040000],
        ['name' => "Al-Dhlail", 'city' => "Zarqa", 'latitude' => 31.960000, 'longitude' => 36.300000],
        ['name' => "Hallabat", 'city' => "Zarqa", 'latitude' => 32.050000, 'longitude' => 36.220000],
        ['name' => "Downtown Irbid", 'city' => "Irbid", 'latitude' => 32.550000, 'longitude' => 35.850000],
        ['name' => "Al-Husn", 'city' => "Irbid", 'latitude' => 32.470000, 'longitude' => 35.890000],
        ['name' => "Beit Ras", 'city' => "Irbid", 'latitude' => 32.570000, 'longitude' => 35.870000],
        ['name' => "Ar-Ramtha", 'city' => "Irbid", 'latitude' => 32.570000, 'longitude' => 36.010000],
        ['name' => "Al-Taybeh", 'city' => "Irbid", 'latitude' => 32.553700, 'longitude' => 35.692900],
        ['name' => "Aydoun", 'city' => "Irbid", 'latitude' => 32.540000, 'longitude' => 35.780000],
        ['name' => "Al-Mazar", 'city' => "Irbid", 'latitude' => 32.460000, 'longitude' => 35.800000],
        ['name' => "As-Sarih", 'city' => "Irbid", 'latitude' => 32.520000, 'longitude' => 35.960000],
        ['name' => "Aqaba City Center", 'city' => "Aqaba", 'latitude' => 29.526200, 'longitude' => 35.006000],
        ['name' => "Al-Quweira", 'city' => "Aqaba", 'latitude' => 29.591700, 'longitude' => 35.515600],
        ['name' => "South Beach", 'city' => "Aqaba", 'latitude' => 29.510000, 'longitude' => 35.000000],
        ['name' => "Industrial Zone", 'city' => "Aqaba", 'latitude' => 29.480000, 'longitude' => 35.010000],
        ['name' => "Madaba Center", 'city' => "Madaba", 'latitude' => 31.718000, 'longitude' => 35.793000],
        ['name' => "Dhiban", 'city' => "Madaba", 'latitude' => 31.498000, 'longitude' => 35.773000],
        ['name' => "Mukawir", 'city' => "Madaba", 'latitude' => 31.630000, 'longitude' => 35.680000],
        ['name' => "As-Salt Center", 'city' => "As-Salt", 'latitude' => 32.039000, 'longitude' => 35.727000],
        ['name' => "Ain Al-Basha", 'city' => "As-Salt", 'latitude' => 32.060000, 'longitude' => 35.760000],
        ['name' => "Mahis", 'city' => "As-Salt", 'latitude' => 31.990000, 'longitude' => 35.700000],
        ['name' => "Mafraq Center", 'city' => "Mafraq", 'latitude' => 32.343000, 'longitude' => 36.205000],
        ['name' => "Safawi", 'city' => "Mafraq", 'latitude' => 32.120000, 'longitude' => 37.130000],
        ['name' => "Ruwaished", 'city' => "Mafraq", 'latitude' => 32.505000, 'longitude' => 38.188000],
        ['name' => "Jerash Center", 'city' => "Jerash", 'latitude' => 32.275000, 'longitude' => 35.900000],
        ['name' => "Sakib", 'city' => "Jerash", 'latitude' => 32.310000, 'longitude' => 35.870000],
        ['name' => "Burma", 'city' => "Jerash", 'latitude' => 32.240000, 'longitude' => 35.870000],
        ['name' => "Ajloun Center", 'city' => "Ajloun", 'latitude' => 32.330000, 'longitude' => 35.750000],
        ['name' => "Kufranjah", 'city' => "Ajloun", 'latitude' => 32.380000, 'longitude' => 35.720000],
        ['name' => "Karak Center", 'city' => "Karak", 'latitude' => 31.180000, 'longitude' => 35.700000],
        ['name' => "Mazar", 'city' => "Karak", 'latitude' => 31.062000, 'longitude' => 35.695000],
        ['name' => "Mu'ta", 'city' => "Karak", 'latitude' => 31.058000, 'longitude' => 35.677000],
        ['name' => "Tafilah Center", 'city' => "Tafilah", 'latitude' => 30.840000, 'longitude' => 35.607000],
        ['name' => "Al-Hasa", 'city' => "Tafilah", 'latitude' => 30.960000, 'longitude' => 35.700000],
        ['name' => "Ma'an Center", 'city' => "Ma'an", 'latitude' => 30.195000, 'longitude' => 35.734000],
        ['name' => "Wadi Musa", 'city' => "Ma'an", 'latitude' => 30.320000, 'longitude' => 35.480000],
        ['name' => "Shoubak", 'city' => "Ma'an", 'latitude' => 30.530000, 'longitude' => 35.560000],
        ['name' => "Ras An-Naqb", 'city' => "Ma'an", 'latitude' => 29.990000, 'longitude' => 35.490000],
    ];

    public function run(): void
    {
        $connection = config('tenancy.landlord_connection', 'landlord');

        foreach ($this->areas as $area) {
            DB::connection($connection)->table('areas')->updateOrInsert(
                ['name' => $area['name'], 'city' => $area['city']],
                ['latitude' => $area['latitude'], 'longitude' => $area['longitude']]
            );
        }
    }
}
