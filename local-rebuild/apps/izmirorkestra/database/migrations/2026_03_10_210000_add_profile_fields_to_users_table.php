<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        try {
            Schema::table('users', function (Blueprint $table): void {
                if (! Schema::hasColumn('users', 'phone')) {
                    $table->string('phone', 32)->nullable()->after('email');
                }
                if (! Schema::hasColumn('users', 'city')) {
                    $table->string('city', 120)->nullable()->after('phone');
                }
                if (! Schema::hasColumn('users', 'district')) {
                    $table->string('district', 120)->nullable()->after('city');
                }
                if (! Schema::hasColumn('users', 'profile_photo_path')) {
                    $table->string('profile_photo_path', 255)->nullable()->after('district');
                }
                if (! Schema::hasColumn('users', 'company_name')) {
                    $table->string('company_name', 255)->nullable()->after('profile_photo_path');
                }
                if (! Schema::hasColumn('users', 'service_area')) {
                    $table->string('service_area', 255)->nullable()->after('company_name');
                }
                if (! Schema::hasColumn('users', 'short_bio')) {
                    $table->text('short_bio')->nullable()->after('service_area');
                }
                if (! Schema::hasColumn('users', 'provided_services')) {
                    $table->text('provided_services')->nullable()->after('short_bio');
                }
                if (! Schema::hasColumn('users', 'website_url')) {
                    $table->string('website_url', 255)->nullable()->after('provided_services');
                }
                if (! Schema::hasColumn('users', 'social_links')) {
                    $table->text('social_links')->nullable()->after('website_url');
                }
            });
        } catch (QueryException $e) {
            $message = strtolower($e->getMessage());
            if ((int) $e->getCode() !== 42_000 && ! str_contains($message, 'duplicate column')) {
                throw $e;
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            foreach ([
                'social_links',
                'website_url',
                'provided_services',
                'short_bio',
                'service_area',
                'company_name',
                'profile_photo_path',
                'district',
                'city',
                'phone',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
