<?php

namespace App\Console\Commands;

use App\Models\SusResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportSusCsv extends Command
{
    protected $signature = 'sus:import-csv {path= : CSV path relative to storage/app or absolute path}';

    protected $description = 'Import legacy SUS CSV responses into sus_responses table idempotently.';

    public function handle(): int
    {
        $path = $this->resolvePath($this->argument('path') ?: 'sus_responses.csv');

        if (! file_exists($path)) {
            $this->error("CSV file not found: {$path}");
            return self::FAILURE;
        }

        $file = fopen($path, 'r');
        if (! $file) {
            $this->error("Unable to open CSV file: {$path}");
            return self::FAILURE;
        }

        $header = fgetcsv($file, 0, ';');
        if (! $header) {
            fclose($file);
            $this->error('CSV header is missing.');
            return self::FAILURE;
        }

        $imported = 0;
        $skipped = 0;
        $invalid = 0;

        while (($row = fgetcsv($file, 0, ';')) !== false) {
            $payload = $this->payloadFromRow($row);

            if (! $payload || ! User::whereKey($payload['user_id'])->exists()) {
                $invalid++;
                continue;
            }

            $duplicate = SusResponse::query()
                ->where('user_id', $payload['user_id'])
                ->where('skor_sus', $payload['skor_sus'])
                ->where('created_at', $payload['created_at'])
                ->where('sus_1', $payload['sus_1'])
                ->where('sus_2', $payload['sus_2'])
                ->where('sus_3', $payload['sus_3'])
                ->where('sus_4', $payload['sus_4'])
                ->where('sus_5', $payload['sus_5'])
                ->where('sus_6', $payload['sus_6'])
                ->where('sus_7', $payload['sus_7'])
                ->where('sus_8', $payload['sus_8'])
                ->where('sus_9', $payload['sus_9'])
                ->where('sus_10', $payload['sus_10'])
                ->exists();

            if ($duplicate) {
                $skipped++;
                continue;
            }

            SusResponse::create($payload);
            $imported++;
        }

        fclose($file);

        $this->info("Imported: {$imported}");
        $this->info("Skipped: {$skipped}");
        $this->info("Invalid: {$invalid}");

        return self::SUCCESS;
    }

    private function resolvePath(string $path): string
    {
        return str_starts_with($path, '/') ? $path : storage_path('app/' . $path);
    }

    private function payloadFromRow(array $row): ?array
    {
        if (count($row) < 17) {
            return null;
        }

        $createdAt = $this->parseDate($row[16] ?? null);
        if (! $createdAt) {
            return null;
        }

        $payload = [
            'user_id' => (int) ($row[0] ?? 0),
            'skor_sus' => (float) ($row[13] ?? 0),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];

        for ($index = 1; $index <= 10; $index++) {
            $value = (int) ($row[$index + 2] ?? 0);
            if ($value < 1 || $value > 5) {
                return null;
            }
            $payload["sus_{$index}"] = $value;
        }

        if ($payload['user_id'] < 1 || $payload['skor_sus'] < 0 || $payload['skor_sus'] > 100) {
            return null;
        }

        return $payload;
    }

    private function parseDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y H:i:s', trim($value), 'Asia/Jakarta')->timezone(config('app.timezone'));
        } catch (\Throwable) {
            return null;
        }
    }
}
