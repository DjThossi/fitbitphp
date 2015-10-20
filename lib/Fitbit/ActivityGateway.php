<?php

namespace Fitbit;

class ActivityGateway extends EndpointGateway {

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
     * @param \DateTime $date Activity date and time (set proper timezone, which could be fetched via getProfile)
     * @param string $activityId Activity Id (or Intensity Level Id) from activities database,
     *                                  see http://wiki.fitbit.com/display/API/API-Log-Activity
     * @param string $duration Duration millis
     * @param string $calories Manual calories to override Fitbit estimate
     * @param string $distance Distance in km/miles (as set with setMetric)
     * @param string $distanceUnit Distance unit string (see http://wiki.fitbit.com/display/API/API-Distance-Unit)
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function logActivity(\DateTime $date, $activityId, $duration, $calories = null, $distance = null, $distanceUnit = null, $activityName = null)
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
