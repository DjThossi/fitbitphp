<?php

namespace Fitbit;

use DateTime;
use SimpleXMLElement;
use stdClass;

class ActivityGateway extends EndpointGateway
{
    const RESOURCE_PATH_CALORIES = 'calories';
    const RESOURCE_PATH_CALORIES_BMR = 'caloriesBMR';
    const RESOURCE_PATH_STEPS = 'steps';
    const RESOURCE_PATH_DISTANCE = 'distance';
    const RESOURCE_PATH_FLOORS = 'floors';
    const RESOURCE_PATH_ELEVATION = 'elevation';
    const RESOURCE_PATH_MINUTES_SECENDARTY = 'minutesSedentary';
    const RESOURCE_PATH_MINUTES_LIGHTLY_ACTIVE = 'minutesLightlyActive';
    const RESOURCE_PATH_MINUTES_FAIRLY_ACTIVE = 'minutesFairlyActive';
    const RESOURCE_PATH_MINUTES_VERY_ACTIVE = 'minutesVeryActive';
    const RESOURCE_PATH_ACTIVITY_CALORIES = 'activityCalories';

    const RESOURCE_PATH_TRACKER_CALORIES = 'tracker/calories';
    const RESOURCE_PATH_TRACKER_CALORIES_BMR = 'tracker/caloriesBMR';
    const RESOURCE_PATH_TRACKER_STEPS = 'tracker/steps';
    const RESOURCE_PATH_TRACKER_DISTANCE = 'tracker/distance';
    const RESOURCE_PATH_TRACKER_FLOORS = 'tracker/floors';
    const RESOURCE_PATH_TRACKER_ELEVATION = 'tracker/elevation';
    const RESOURCE_PATH_TRACKER_MINUTES_SECENDARTY = 'tracker/minutesSedentary';
    const RESOURCE_PATH_TRACKER_MINUTES_LIGHTLY_ACTIVE = 'tracker/minutesLightlyActive';
    const RESOURCE_PATH_TRACKER_MINUTES_FAIRLY_ACTIVE = 'tracker/minutesFairlyActive';
    const RESOURCE_PATH_TRACKER_MINUTES_VERY_ACTIVE = 'tracker/minutesVeryActive';
    const RESOURCE_PATH_TRACKER_ACTIVITY_CALORIES = 'tracker/activityCalories';

    /**
     * Get user's activity statistics (lifetime statistics from the tracker device and total numbers including the manual activity log entries)
     *
     * @throws Exception
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function getActivityStats()
    {
        return $this->makeApiRequest('user/' . $this->userID . '/activities');
    }

    /**
     * Get user activities for specific date
     *
     * @param string $resourcePath
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param string $endDatePeriod
     *
     * @return SimpleXMLElement|stdClass
     *
     * @throws Exception
     */
    public function getTimeSeries($resourcePath, DateTime $startDate, DateTime $endDate = null, $endDatePeriod = null)
    {
        if ($endDate === null && $endDatePeriod === null) {
            throw new Exception('$endDate or $endDatePeriod must be provided');
        }

        if ($endDatePeriod === null) {
            $endDatePeriod = $endDate->format('Y-m-d');
        }

        return $this->makeApiRequest(
            'user/' . $this->getUserID() . '/activities' . $resourcePath .
            '/date/' . $startDate->format('Y-m-d') . '/' . $endDatePeriod
        );
    }

    /**
     * Get user activities for specific date
     *
     * @throws Exception
     * @param  \DateTime $date
     * @param  String $dateStr
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function getActivities($date, $dateStr = null)
    {
        if (!isset($dateStr)) {
            $dateStr = $date->format('Y-m-d');
        }

        return $this->makeApiRequest('user/' . $this->userID . '/activities/date/' . $dateStr);
    }

    /**
     * Get user recent activities
     *
     * @throws Exception
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function getRecentActivities()
    {
        return $this->makeApiRequest('user/-/activities/recent');
    }

    /**
     * Get user daily goals
     *
     * @throws Exception
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function getDailyGoals()
    {
        return $this->makeApiRequest('user/-/activities/goals/daily');
    }

    /**
     * Get user weekly goals
     *
     * @throws Exception
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function getWeeklyGoals()
    {
        return $this->makeApiRequest('user/-/activities/goals/weekly');
    }

    /**
     * Get user frequent activities
     *
     * @throws Exception
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function getFrequentActivities()
    {
        return $this->makeApiRequest('user/-/activities/frequent');
    }

    /**
     * Get user favorite activities
     *
     * @throws Exception
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function getFavoriteActivities()
    {
        return $this->makeApiRequest('user/-/activities/favorite');
    }

    /**
     * Log user activity
     *
     * @throws Exception
     * @param DateTime $date Activity date and time (set proper timezone, which could be fetched via getProfile)
     * @param string $activityId Activity Id (or Intensity Level Id) from activities database,
     *                                  see http://wiki.fitbit.com/display/API/API-Log-Activity
     * @param string $duration Duration millis
     * @param string $calories Manual calories to override Fitbit estimate
     * @param string $distance Distance in km/miles (as set with setMetric)
     * @param string $distanceUnit Distance unit string (see http://wiki.fitbit.com/display/API/API-Distance-Unit)
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function logActivity(DateTime $date, $activityId, $duration, $calories = null, $distance = null, $distanceUnit = null, $activityName = null)
    {
        $distanceUnits = array('Centimeter', 'Foot', 'Inch', 'Kilometer', 'Meter', 'Mile', 'Millimeter', 'Steps', 'Yards');

        $parameters = array();
        $parameters['date'] = $date->format('Y-m-d');
        $parameters['startTime'] = $date->format('H:i');
        if (isset($activityName)) {
            $parameters['activityName'] = $activityName;
            $parameters['manualCalories'] = $calories;
        } else {
            $parameters['activityId'] = $activityId;
            if (isset($calories))
                $parameters['manualCalories'] = $calories;
        }
        $parameters['durationMillis'] = $duration;
        if (isset($distance))
            $parameters['distance'] = $distance;
        if (isset($distanceUnit) && in_array($distanceUnit, $distanceUnits))
            $parameters['distanceUnit'] = $distanceUnit;

        return $this->makeApiRequest('user/-/activities', 'POST', $parameters);
    }

    /**
     * Delete user activity
     *
     * @throws Exception
     * @param string $id Activity log id
     * @return bool
     */
    public function deleteActivity($id)
    {
        return $this->makeApiRequest('user/-/activities/' . $id, 'DELETE');
    }

    /**
     * Add user favorite activity
     *
     * @throws Exception
     * @param string $id Activity log id
     * @return bool
     */
    public function addFavoriteActivity($id)
    {
        return $this->makeApiRequest('user/-/activities/log/favorite/' . $id, 'POST');
    }

    /**
     * Delete user favorite activity
     *
     * @throws Exception
     * @param string $id Activity log id
     * @return bool
     */
    public function deleteFavoriteActivity($id)
    {
        return $this->makeApiRequest('user/-/activities/log/favorite/' . $id, 'DELETE');
    }

    /**
     * Get full description of specific activity
     *
     * @throws Exception
     * @param  string $id Activity log Id
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function getActivity($id)
    {
        return $this->makeApiRequest('activities/' . $id);
    }

    /**
     * Get a tree of all valid Fitbit public activities as well as private custom activities the user createds
     *
     * @throws Exception
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function browseActivities()
    {
        return $this->makeApiRequest('activities');
    }
}
