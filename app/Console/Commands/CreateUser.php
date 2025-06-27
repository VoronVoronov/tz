<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Balance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Класс команды создания пользователя
 *
 * Создаёт нового пользователя с начальным балансом
 */
class CreateUser extends Command
{
    /**
     * Сигнатура консольной команды
     *
     * @var string
     */
    protected $signature = 'user:create {name} {email} {password}';

    /**
     * Описание консольной команды
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Выполнение консольной команды
     *
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        try {
            $user = $this->createUserRecord($name, $email, $password);
            $this->createInitialBalance($user->id);

            $this->info("User created successfully with ID: {$user->id}");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Создание новой записи пользователя
     *
     * @param string $name     Имя пользователя
     * @param string $email    Email пользователя
     * @param string $password Пароль пользователя
     * 
     * @return User
     */
    private function createUserRecord(
        string $name,
        string $email,
        string $password
    ): User {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password)
        ]);
    }

    /**
     * Создание начального баланса для пользователя
     *
     * @param int $userId ID пользователя
     * 
     * @return void
     */
    private function createInitialBalance(int $userId): void
    {
        Balance::create([
            'user_id' => $userId,
            'amount' => 0
        ]);
    }
}
