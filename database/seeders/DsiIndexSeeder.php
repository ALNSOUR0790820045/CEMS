<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DsiIndex;
use Carbon\Carbon;

class DsiIndexSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample DSI indices for Jordan (2024-2026)
        // These are example values - in production, you would import real data from DOS Jordan
        
        $indices = [
            // 2024 Data
            ['year' => 2024, 'month' => 1, 'materials' => 145.2, 'labor' => 132.5],
            ['year' => 2024, 'month' => 2, 'materials' => 146.1, 'labor' => 133.2],
            ['year' => 2024, 'month' => 3, 'materials' => 147.3, 'labor' => 134.0],
            ['year' => 2024, 'month' => 4, 'materials' => 148.5, 'labor' => 134.8],
            ['year' => 2024, 'month' => 5, 'materials' => 149.2, 'labor' => 135.5],
            ['year' => 2024, 'month' => 6, 'materials' => 150.1, 'labor' => 136.2],
            ['year' => 2024, 'month' => 7, 'materials' => 151.3, 'labor' => 137.1],
            ['year' => 2024, 'month' => 8, 'materials' => 152.0, 'labor' => 137.8],
            ['year' => 2024, 'month' => 9, 'materials' => 153.2, 'labor' => 138.6],
            ['year' => 2024, 'month' => 10, 'materials' => 154.5, 'labor' => 139.4],
            ['year' => 2024, 'month' => 11, 'materials' => 155.3, 'labor' => 140.2],
            ['year' => 2024, 'month' => 12, 'materials' => 156.0, 'labor' => 141.0],
            
            // 2025 Data
            ['year' => 2025, 'month' => 1, 'materials' => 156.8, 'labor' => 142.3],
            ['year' => 2025, 'month' => 2, 'materials' => 158.2, 'labor' => 143.1],
            ['year' => 2025, 'month' => 3, 'materials' => 159.5, 'labor' => 144.0],
            ['year' => 2025, 'month' => 4, 'materials' => 160.8, 'labor' => 144.8],
            ['year' => 2025, 'month' => 5, 'materials' => 162.0, 'labor' => 145.6],
            ['year' => 2025, 'month' => 6, 'materials' => 163.4, 'labor' => 146.5],
            ['year' => 2025, 'month' => 7, 'materials' => 164.7, 'labor' => 147.3],
            ['year' => 2025, 'month' => 8, 'materials' => 165.9, 'labor' => 148.1],
            ['year' => 2025, 'month' => 9, 'materials' => 167.2, 'labor' => 149.0],
            ['year' => 2025, 'month' => 10, 'materials' => 168.5, 'labor' => 149.8],
            ['year' => 2025, 'month' => 11, 'materials' => 169.8, 'labor' => 150.7],
            ['year' => 2025, 'month' => 12, 'materials' => 171.0, 'labor' => 151.5],
            
            // 2026 Data (First Quarter)
            ['year' => 2026, 'month' => 1, 'materials' => 172.3, 'labor' => 152.4],
            ['year' => 2026, 'month' => 2, 'materials' => 173.5, 'labor' => 153.2],
            ['year' => 2026, 'month' => 3, 'materials' => 174.8, 'labor' => 154.1],
        ];
        
        foreach ($indices as $index) {
            $indexDate = Carbon::createFromDate($index['year'], $index['month'], 1);
            
            DsiIndex::create([
                'index_date' => $indexDate,
                'year' => $index['year'],
                'month' => $index['month'],
                'materials_index' => $index['materials'],
                'labor_index' => $index['labor'],
                'general_index' => ($index['materials'] + $index['labor']) / 2, // Average
                'source' => 'DOS Jordan',
                'reference_url' => 'http://www.dos.gov.jo',
            ]);
        }
        
        // Calculate change percentages for all indices
        $allIndices = DsiIndex::orderBy('year')->orderBy('month')->get();
        foreach ($allIndices as $idx) {
            $idx->calculateChangePercent();
        }
        
        $this->command->info('DSI indices seeded successfully!');
    }
}
