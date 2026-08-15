<?php
namespace App\Console\Commands;

use App\Models\TeacherContract;
use Illuminate\Console\Command;

class UpdateExpiredContracts extends Command
{
protected $signature = 'contracts:update-expired';
protected $description = 'Met à jour le statut des contrats expirés';

public function handle(): int
{
$count = TeacherContract::where('status', 'active')
->whereNotNull('end_date')
->where('end_date', '<', now()) ->update(['status' => 'expired']);

    $this->info("{$count} contrat(s) marqué(s) comme expiré(s).");
    return self::SUCCESS;
    }
    }