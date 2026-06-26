php artisan make:migration create_products_table

php artisan make:migration create_product_variants_table

php artisan make:migration create_portfolio_categories_table
php artisan make:migration create_locations_table
php artisan make:migration create_sectors_table
php artisan make:migration create_installation_types_table

php artisan make:migration create_portfolio_projects_table


<!-- Pivot table -->
php artisan make:migration create_portfolio_project_variant_table
php artisan make:migration create_portfolio_project_sector_table
php artisan make:migration create_portfolio_project_installation_type_table
php artisan make:migration create_media_table

php artisan migrate
//success

//model

php artisan make:model Product
php artisan make:model ProductVariant
php artisan make:model PortfolioCategory
php artisan make:model Location
php artisan make:model Sector
php artisan make:model InstallationType
php artisan make:model PortfolioProject
php artisan make:model Media

<!-- delte -->


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('model_type');
            $table->morphs('model_id');
            $table->string('s3_path');
            $table->string('alt_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('create_media_tables');
    }
};
<!-- end -->

composer require spatie/laravel-medialibrary

# Publishes Spatie's own media migration into your database/migrations folder
php artisan vendor:publish \
  --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" \
  --tag="medialibrary-migrations"

php artisan migrate
<!-- Filament create -already installed -->
composer require filament/filament:"^3.0" -W
php artisan filament:install --panels
php artisan make:filament-user    # creates your admin login

php artisan make:filament-resource PortfolioCategory --generate
> ask title attribute give either name of the column present in table (name, title)
>it ask for read only resource (recommended NO)


php artisan make:filament-resource Sector --generate
php artisan make:filament-resource InstallationType --generate
php artisan make:filament-resource Location --generate

heroicons.com

php artisan make:filament-resource Product --generate
php artisan make:filament-relation-manager ProductResource variants name
# "ProductResource" = the parent resource class name
# "variants"        = the method name on Product model ($product->variants)
# "name"            = the column shown in the relation manager table

answer [no no]
php artisan make:filament-resource PortfolioProject --generate

<!-- Not needed 
php artisan make:filament-relation-manager PortfolioProjectResource sectors name
php artisan make:filament-relation-manager PortfolioProjectResource installationTypes name
php artisan make:filament-relation-manager PortfolioProjectResource productVariants name -->

composer require bezhansalleh/filament-shield
php artisan shield:install
php artisan shield:generate --all 



# Spatie MediaLibrary — ORM integration
composer require spatie/laravel-medialibrary



# Filament plugin for Spatie upload field in forms
# Check your exact Filament v4 version first:
composer require filament/spatie-laravel-media-library-plugin

# Publish Spatie's migration (the one you should never hand-write)
php artisan vendor:publish \
  --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" \
  --tag="medialibrary-migrations"

# Cloudinary filesystem driver Not sutiable for laravel 13
composer require cloudinary-labs/cloudinary-laravel 
# Cloudinary publishes its config
php artisan cloudinary:install

php artisan migrate



composer require codebar-ag/laravel-flysystem-cloudinary
php artisan vendor:publish --tag="flysystem-cloudinary-config"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-config"

<!-- php artisan livewire:publish --config -->