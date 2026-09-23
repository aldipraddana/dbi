<?php

namespace App\Console\Commands;

use App\Models\UserMenu;
use Filament\Commands\MakeUserCommand as FilamentMakeUserCommand;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\multisearch;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class MakeUserCommand extends FilamentMakeUserCommand
{
    protected $description = 'Create a new user';

    protected $signature = 'make:filament-user
                            {--name= : The name of the user}
                            {--username= : A valid and unique username}
                            {--password= : The password for the user (min. 8 characters)}';

    /**
     * @var array{'name': string | null, 'username': string | null, 'password': string | null}
     */
    protected array $options;

    /**
     * @return array{'name': string, 'username': string, 'password': string}
     */
    protected function getUserData(): array
    {
        return [
            'name' => $this->options['name'] ?? text(
                label: 'Name',
                required: true,
            ),

            'username' => $this->options['username'] ?? text(
                label: 'Username',
                required: true,
                validate: fn (string $username): ?string => match (true) {
                    static::getUserModel()::where('username', $username)->exists() => 'A user with this username already exists',
                    default => null,
                },
            ),

            'password' => Hash::make($this->options['password'] ?? password(
                label: 'Password',
                required: true,
            )),

            'menus' => $this->options['menus'] ?? multisearch(
                label: 'Menus',
                required: true,
                options: function(): array {
                    $menus = UserMenu::pluck('name', 'id')->toArray();

                    return $menus;
                },
                validate: fn (array $menus): ?string => match (true) {
                    empty($menus) => 'You must select at least one menu',
                    default => null,
                },
            ),
        ];
    }

    protected function createUser(): Authenticatable
    {
        $data = $this->getUserData();

        /** @var \App\Models\User $user */
        $user = static::getUserModel()::create($data);
        $user->menus()->sync($data['menus']);

        return $user;
    }

    /**
     * @param \App\Models\User $user
     * @return void
     */
    protected function sendSuccessMessage(Authenticatable $user): void
    {
        $loginUrl = Filament::getLoginUrl();

        $this->components->info('Success! ' . ($user->getAttribute('username') ?? $user->getAttribute('username') ?? 'You') . " may now log in at {$loginUrl}");
    }

    public function handle(): int
    {
        $this->options = $this->options();

        if (! Filament::getCurrentPanel()) {
            $this->error('Filament has not been installed yet: php artisan filament:install --panels');

            return static::INVALID;
        }

        $user = $this->createUser();
        $this->sendSuccessMessage($user);

        return static::SUCCESS;
    }
}
