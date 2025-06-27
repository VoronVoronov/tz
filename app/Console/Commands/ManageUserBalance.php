<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\ProcessBalanceOperation;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * Команда управления балансом
 *
 * @category Console
 * @package  App\Console\Commands
 */
class ManageUserBalance extends Command
{
    /**
     * Сигнатура консольной команды
     *
     * @var string
     */
    protected $signature = 
        'balance:manage {email} {amount} {description} {--action=add}';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Manage user balance (add/subtract)';

    /**
     * Выполнение консольной команды
     *
     * @return void
     */
    public function handle(): void
    {
        $email = $this->argument('email');
        $amount = (float) $this->argument('amount');
        $description = $this->argument('description');
        $action = $this->option('action');

        try {
            $user = User::where('email', $email)->firstOrFail();
            
            ProcessBalanceOperation::dispatch(
                $user->id,
                $amount,
                $action === 'add' ? 'deposit' : 'withdrawal',
                $description
            );

            $this->info('Операция поставлена в очередь');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }


}
