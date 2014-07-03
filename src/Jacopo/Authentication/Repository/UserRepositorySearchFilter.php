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
  use ConnectionTrait;

    private $per_page;
    private $user_table_name = "users";
    private $profile_table_name;
    private $user_groups_table_name;
    private $groups_table_name;

    public function __construct($per_page = 5)
    {
        $this->user_groups_table_name = "users_groups";
        $this->per_page = $per_page;
        $this->profile_table_name= App::make('profile_repository')->getModel()->getTable();
        $this->groups_table_name = 'groups';
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

        return $q->paginate($this->per_page);
    }

    /**
     * @return mixed
     */
    private function createTableJoins()
    {
        $q = DB::connection($this->getConnectionName());
        $q = $q->table($this->user_table_name)
            ->leftJoin($this->profile_table_name, $this->user_table_name . '.id', '=', $this->profile_table_name. '.user_id')
            ->leftJoin($this->user_groups_table_name, $this->user_table_name . '.id', '=', $this->user_groups_table_name . '.user_id')
            ->leftJoin($this->groups_table_name, $this->user_groups_table_name . '.group_id', '=',$this->groups_table_name . '.id');

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
        if($this->isSettedInputFilter($input_filter)) foreach ($input_filter as $column => $value) {
            if($this->isValidFilterValue($value)) switch ($column) {
                case 'activated':
                    $q = $q->where($this->user_table_name . '.activated', '=', $value);
                    break;
                case 'banned':
                    $q = $q->where($this->user_table_name . '.banned', '=', $value);
                    break;
                case 'email':
                    $q = $q->where($this->user_table_name. '.email', 'LIKE', "%{$value}%");
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
                    $q = $q->where($this->groups_table_name . '.id' , '=', $value);
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

        if ( $this->isValidOrderingFilter($input_filter) )
        {
            $ordering = $this->guessOrderingType($input_filter);
            $q = $q->orderBy($input_filter['order_by'], $ordering);
        }

        return $q;
    }

    /**
     * @param $filter
     * @return bool
     */
    public function isValidOrderingFilter($input_filter)
    {
        $valid_ordering_fields = ["first_name", "last_name", "email", "last_login", "activated", "name"];

        if( ! isset($input_filter['order_by']) ) return false;

        $order_by_filter = $input_filter['order_by'];

        if( empty($order_by_filter) ) return false;
        return in_array($order_by_filter, $valid_ordering_fields);

    }

    /**
     * @param array $input_filter
     * @return string
     */
    private function guessOrderingType(array $input_filter)
    {
        return $ordering = (isset($input_filter['ordering']) && $input_filter['ordering'] == 'desc') ? 'DESC' : 'ASC';
    }

    /**
     * @param $q
     * @return mixed
     */
    private function createAllSelect($q)
    {
        $q = $q->select(
            // for postgres i need to use distinct on here
            DB::RAW( "DISTINCT ON (".$this->user_table_name . ".email) {$this->user_table_name}.email"),
            $this->user_table_name . ".*",
            $this->profile_table_name. '.first_name',
            $this->profile_table_name. '.last_name',
            $this->profile_table_name. '.zip',
            $this->profile_table_name. '.code',
            $this->groups_table_name . '.name'
        );

//        $q = $q->groupBy($this->user_table_name.'.email');

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