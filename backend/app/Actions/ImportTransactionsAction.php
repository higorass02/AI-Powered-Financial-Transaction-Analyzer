<?php

namespace App\Actions;

use App\Events\TransactionCreated;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportTransactionsAction
{
    private const MAX_LINES = 10000;
    private const VALID_TYPES = ['debit', 'credit', 'pix'];

    public function execute(int $userId, UploadedFile $file, bool $autoCategorize = true): array
    {
        $rows = $this->parseCSV($file);
        $imported = 0;
        $errors = [];

        DB::transaction(function () use ($userId, $rows, $autoCategorize, &$imported, &$errors) {
            foreach ($rows as $index => $row) {
                $lineNumber = $index + 2; // +2 for header and 1-based

                try {
                    $this->validateRow($row, $lineNumber);

                    $transaction = Transaction::create([
                        'user_id' => $userId,
                        'description' => trim($row['description']),
                        'amount' => (float) $row['amount'],
                        'type' => strtolower(trim($row['type'])),
                        'date' => Carbon::parse($row['date'])->toDateString(),
                        'status' => 'approved',
                    ]);

                    if ($autoCategorize) {
                        TransactionCreated::dispatch($transaction);
                    }

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = ['line' => $lineNumber, 'error' => $e->getMessage()];
                    Log::warning("Import line {$lineNumber} failed: " . $e->getMessage());
                }
            }
        });

        return [
            'imported' => $imported,
            'errors' => count($errors),
            'error_details' => $errors,
        ];
    }

    private function parseCSV(UploadedFile $file): array
    {
        $rows = [];
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            throw new \InvalidArgumentException('CSV file is empty or invalid.');
        }

        $header = array_map(fn ($h) => strtolower(trim($h)), $header);
        $requiredColumns = ['description', 'amount', 'type', 'date'];
        $missing = array_diff($requiredColumns, $header);

        if (!empty($missing)) {
            fclose($handle);
            throw new \InvalidArgumentException('Missing required columns: ' . implode(', ', $missing));
        }

        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if ($count >= self::MAX_LINES) {
                break;
            }
            $rows[] = array_combine($header, $row);
            $count++;
        }

        fclose($handle);
        return $rows;
    }

    private function validateRow(array $row, int $line): void
    {
        if (empty($row['description']) || strlen(trim($row['description'])) < 3) {
            throw new \InvalidArgumentException("Description too short or empty.");
        }

        $amount = (float) $row['amount'];
        if ($amount <= 0 || $amount > 100000) {
            throw new \InvalidArgumentException("Amount must be between 0.01 and 100,000.");
        }

        if (!in_array(strtolower(trim($row['type'])), self::VALID_TYPES)) {
            throw new \InvalidArgumentException("Invalid type '{$row['type']}'. Must be debit, credit, or pix.");
        }

        try {
            $date = Carbon::parse($row['date']);
            if ($date->isFuture()) {
                throw new \InvalidArgumentException("Date cannot be in the future.");
            }
        } catch (\Exception) {
            throw new \InvalidArgumentException("Invalid date format: {$row['date']}");
        }
    }
}
