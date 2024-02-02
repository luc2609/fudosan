<?php

namespace App\Rules;

use App\Models\MasterStation;
use Illuminate\Contracts\Validation\Rule;

class RailStationRule implements Rule
{
    protected $rail_stations;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($rail_stations)
    {
        $this->rail_stations = $rail_stations;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $railStations = $this->rail_stations;

        if (!empty($railStations)) {
            foreach ($railStations as $railStation) {
                if (is_string($railStation)) {
                    $railStation = json_decode($railStation, true);
                }

                if (!isset($railStation['rail_cd']) || !isset($railStation['station_cd'])) {
                    return false;
                }

                $railCd = $railStation['rail_cd'];
                $stationCd = $railStation['station_cd'];

                $masterStation = MasterStation::where('rail_cd', $railCd)->where('cd', $stationCd)->first();

                if (!$masterStation) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Rail station error';
    }
}
