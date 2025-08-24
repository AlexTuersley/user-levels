# Publish the Files
php artisan vendor:publish --provider="alextuersley\Userlevels\UserLevelServiceProvider"

# Code to Copy files out of package
File::copyDirectory(__DIR__.'/Models/', base_path('app/Models/'));
File::copyDirectory(__DIR__.'/Helpers/', base_path('app/Helpers/'));
File::copyDirectory(__DIR__.'/Services/', base_path('app/Services/'));
File::copyDirectory(__DIR__.'/Traits/', base_path('app/Traits/'));