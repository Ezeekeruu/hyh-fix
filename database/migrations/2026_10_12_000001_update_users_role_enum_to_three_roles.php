<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ROLES = ['admin', 'technician', 'sales-clerk'];

    /**
     * Narrow users.role from admin/staff to the three real roles.
     * Legacy 'staff' rows become 'sales-clerk' first (same access as before),
     * so the narrowed constraint stays valid on every driver.
     */
    public function up(): void
    {
        DB::table('users')->where('role', 'staff')->update(['role' => 'sales-clerk']);

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin', 'technician', 'sales-clerk') NOT NULL DEFAULT 'sales-clerk'");
        } elseif ($driver === 'pgsql') {
            $this->replacePostgresCheck('users', 'role', self::ROLES, 'sales-clerk');
        } elseif ($driver === 'sqlite') {
            // sqlite bakes the enum CHECK into CREATE TABLE, so rebuild the
            // table with the widened CHECK (same columns, same order).
            $this->rebuildSqliteUsersTable(self::ROLES, 'sales-clerk');
        }
    }

    public function down(): void
    {
        DB::table('users')->whereIn('role', ['technician', 'sales-clerk'])->update(['role' => 'staff']);

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin', 'staff') NOT NULL DEFAULT 'staff'");
        } elseif ($driver === 'pgsql') {
            $this->replacePostgresCheck('users', 'role', ['admin', 'staff'], 'staff');
        } elseif ($driver === 'sqlite') {
            $this->rebuildSqliteUsersTable(['admin', 'staff'], 'staff');
        }
    }

    /**
     * sqlite has no DROP CONSTRAINT, and other tables hold FKs to users,
     * so copy into a fresh table (constraints suspended), then swap names.
     */
    private function rebuildSqliteUsersTable(array $allowed, string $default): void
    {
        Schema::disableForeignKeyConstraints();

        try {
            Schema::create('users_new', function (Blueprint $table) use ($allowed, $default) {
                $table->id();
                $table->string('name', 100);
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password', 255);
                $table->enum('role', $allowed)->default($default);
                $table->enum('status', ['active', 'disabled'])->default('active');
                $table->rememberToken();
                $table->timestamps();
            });

            DB::statement('INSERT INTO "users_new" SELECT * FROM "users"');

            Schema::drop('users');
            Schema::rename('users_new', 'users');
        } finally {
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Laravel stores Postgres enums as varchar + CHECK, so swap the CHECK
     * constraint (looked up by column instead of assumed name) and the default.
     */
    private function replacePostgresCheck(string $table, string $column, array $allowed, string $default): void
    {
        $constraints = DB::select(
            "SELECT c.conname FROM pg_constraint c
             JOIN pg_class t ON t.oid = c.conrelid
             JOIN pg_attribute a ON a.attrelid = t.oid AND a.attnum = ANY (c.conkey)
             WHERE t.relname = ? AND a.attname = ? AND c.contype = 'c'",
            [$table, $column]
        );

        DB::statement("ALTER TABLE \"{$table}\" ALTER COLUMN \"{$column}\" DROP DEFAULT");

        foreach ($constraints as $constraint) {
            DB::statement("ALTER TABLE \"{$table}\" DROP CONSTRAINT \"{$constraint->conname}\"");
        }

        $list = implode(', ', array_map(fn ($v) => "'{$v}'", $allowed));
        DB::statement("ALTER TABLE \"{$table}\" ADD CONSTRAINT \"{$table}_{$column}_check\" CHECK (\"{$column}\" IN ({$list}))");
        DB::statement("ALTER TABLE \"{$table}\" ALTER COLUMN \"{$column}\" SET DEFAULT '{$default}'");
    }
};
