<?php  namespace Jacopo\Authentication\Repository;

/**
 * Class UserRepositorySearchFilter
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use App;
use DB;
use Jacopo\Library\Traits\ConnectionTrait;

class UserRepositorySearchFilter
{
    public static $multiple_ordering_separator = ",";
    use ConnectionTrait;

    private $per_page;
    private $user_table_name = "users";
    private $user_groups_table_name = "users_groups";
    private $groups_table_name = "groups";
    private $profile_table_name = "user_profile";
    private $valid_ordering_fields = ["first_name", "last_name", "email", "last_login", "activated", "name"];

    public function __construct($per_page = 5)
    {
        $this->per_page = $per_page;
    }

    /**
     * @param array $input_filter
     * @return mixed|void
     */
    public function all(array $input_filter = [])
    {
        $q = $this->createTableJoins();

        $q = $this->applySearchFilters($input_filter, $q);

        $q = $this->applyOrderingFilter($input_filter, $q);

        $q = $this->createAllSelect($q);

        $users = $q->get();

        $user_emails = array_flip(array_map((function ($element)
        {
            return $element->email;
        }), $users));
        $users_emails_unique = array_unique($user_emails);
        $results = array_only($users, array_values($users_emails_unique));

        return App::make('paginator')->make($results, count($results), $this->per_page);
    }

    /**
     * @return mixed
     */
    private function createTableJoins()
    {
        $q = DB::connection($this->getConnectionName());
        $q = $q->table($this->user_table_name)
               ->leftJoin($this->profile_table_name, $this->user_table_name . '.id', '=', $this->profile_table_name . '.user_id')
               ->leftJoin($this->user_groups_table_name, $this->user_table_name . '.id', '=', $this->user_groups_table_name . '.user_id')
               ->leftJoin($this->groups_table_name, $this->user_groups_table_name . '.group_id', '=', $this->groups_table_name . '.id');

        return $q;
    }

    /**
     * @param array $input_filter
     * @param       $q
     * @param       $user_table
     * @param       $profile_table
     * @pram        $group_table
     * @return mixed
     */
    private function applySearchFilters(array $input_filter = null, $q)
    {
        if($this->isSettedInputFilter($input_filter))
        {
            foreach($input_filter as $column => $value)
            {
                if($this->isValidFilterValue($value))
                {
                    switch($column)
                    {
                        case 'activated':
                            $q = $q->where($this->user_table_name . '.activated', '=', $value);
                            break;
                        case 'banned':
                            $q = $q->where($this->user_table_name . '.banned', '=', $value);
                            break;
                        case 'email':
                            $q = $q->where($this->user_table_name . '.email', 'LIKE', "%{$value}%");
                            break;
                        case 'first_name':
                            $q = $q->where($this->profile_table_name . '.first_name', 'LIKE', "%{$value}%");
                            break;
                        case 'last_name':
                            $q = $q->where($this->profile_table_name . '.last_name', 'LIKE', "%{$value}%");
                            break;
                        case 'zip':
                            $q = $q->where($this->profile_table_name . '.zip', '=', $value);
                            break;
                        case 'code':
                            $q = $q->where($this->profile_table_name . '.code', '=', $value);
                            break;
                        case 'group_id':
                            $q = $q->where($this->groups_table_name . '.id', '=', $value);
                    }
                }
            }
        }

        return $q;
    }

    /**
     * @param array $input_filter
     * @return array
     */
    private function isSettedInputFilter(array $input_filter)
    {
        return $input_filter;
    }

    /**
     * @param $value
     * @return bool
     */
    private function isValidFilterValue($value)
    {
        return $value !== '';
    }

    /**
     * @param array $input_filter
     * @param       $q
     * @return mixed
     */
    private function applyOrderingFilter(array $input_filter, $q)
    {
        if($this->isNotGivenAnOrderingFilter($input_filter)) return $q;

        foreach($this->makeOrderingFilterArray($input_filter) as $field => $ordering)
           if($this->isValidOrderingField($field)) $q = $this->orderByField($field, $this->guessOrderingType($ordering), $q);

        return $q;
    }

    private function orderByField($field, $ordering, $q)
    {
        return $q->orderBy($field, $ordering);
    }

    /**
     * @param array $input_filter
     * @return bool
     */
    private function isNotGivenAnOrderingFilter(array $input_filter)
    {
        return empty($input_filter['order_by'])||empty($input_filter['ordering']);
    }

    /**
     * @param array $input_filter
     * @return array
     */
    private function makeOrderingFilterArray(array $input_filter)
    {
        $order_by = explode(static::$multiple_ordering_separator, $input_filter["order_by"]);
        $ordering = explode(static::$multiple_ordering_separator, $input_filter["ordering"]);

        return array_combine($order_by, $ordering);
    }

    /**
     * @param $filter
     * @return bool
     */
    public function isValidOrderingField($ordering_field)
    {
        return in_array($ordering_field, $this->valid_ordering_fields);
    }

    /**
     * @param array $input_filter
     * @return string
     */
    private function guessOrderingType($ordering)
    {
        return ($ordering == 'desc') ? 'DESC' : 'ASC';
    }

    /**
     * @param $q
     * @return mixed
     */
    private function createAllSelect($q)
    {
        $q = $q->select(
               $this->user_table_name . '.*',
               $this->profile_table_name . '.first_name',
               $this->profile_table_name . '.last_name',
               $this->profile_table_name . '.zip',
               $this->profile_table_name . '.code',
               $this->groups_table_name . '.name'
        );

        return $q;
    }

    /**
     * @param int $per_page
     */
    public function setPerPage($per_page)
    {
        $this->per_page = $per_page;
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->per_page;
    }
}