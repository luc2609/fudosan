<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Paginator::useBootstrap();

        $repositories = [
            'Base',
            'Calendar',
            'CalendarFile',
            'CalendarUser',
            'Certificate',
            'Company',
            'Contact',
            'Customer',
            'CustomerCustomValue',
            'CustomField',
            'Division',
            'File',
            'MasterAdvertisingForm',
            'MasterAdvertisingWeb',
            'MasterBrokerageFee',
            'MasterContactMethod',
            'MasterContactReason',
            'MasterField',
            'MasterNotifyCalendar',
            'MasterPhaseProject',
            'MasterPosition',
            'MasterPostalCode',
            'MasterPrice',
            'MasterPropertyBuildingStructure',
            'MasterPropertyContractType',
            'MasterPropertyCurrentSituation',
            'MasterPropertyType',
            'MasterProvince',
            'MasterPurchasePurpose',
            'MasterRail',
            'MasterResidenceYear',
            'MasterSalePurpose',
            'MasterScheduleRepeat',
            'MasterStation',
            'PasswordReset',
            'PasswordSecurity',
            'Project',
            'ProjectCustomer',
            'ProjectFile',
            'ProjectHistory',
            'ProjectPhase',
            'ProjectProperty',
            'ProjectUser',
            'Property',
            'PropertyCustomValue',
            'PropertyFile',
            'PropertyStation',
            'Role',
            'SubCalendar',
            'SubCalendarFile',
            'SubCalendarUser',
            'User',
            'UserDeviceToken',
            'UserDivision',
            'UserRole',
            'CustomerAdvertisingForm',
            'CustomerPurchasePurpose',
            'UserColor'
        ];
        foreach ($repositories as $repo) {
            $this->app->bind(
                'App\\Repositories\\' . $repo . '\\' . $repo . 'RepositoryInterface',
                'App\\Repositories\\' . $repo . '\\' . $repo . 'EloquentRepository'
            );
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
