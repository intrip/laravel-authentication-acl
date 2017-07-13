<?php  namespace LaravelAcl\Authentication\Repository;
/**
 * Class GroupRepository
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LaravelAcl\Library\Repository\Interfaces\BaseRepositoryInterface;
use LaravelAcl\Authentication\Models\Group;
use LaravelAcl\Authentication\Exceptions\UserNotFoundException as NotFoundException;
use App, Event;
use Cartalyst\Sentry\Groups\GroupNotFoundException;
use LaravelAcl\Library\Repository\EloquentBaseRepository;

class SentryGroupRepository extends EloquentBaseRepository implements BaseRepositoryInterface
{
    /**
     * Sentry instance
     * @var
     */
    protected $sentry;

    protected $config_reader;

    protected $groupModel =  \LaravelAcl\Authentication\Models\Group::class;

    public function __construct($config_reader = null)
    {
        $this->sentry = App::make('sentry');
        $this->config_reader = $config_reader ? $config_reader : App::make('config');

        if (method_exists($this->sentry, 'getGroupProvider')) {
            $this->groupModel = get_class( $this->sentry->getGroupProvider()->createModel());
        }

        return parent::__construct( new $this->groupModel );
    }
    /**
     * Create a new object
     *
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->sentry->createGroup($data);
    }

    /**
     * Update a new object
     *
     * @param       id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data)
    {
        $obj = $this->find($id);
        Event::fire('repository.updating', [$obj]);
        $obj->update($data);
        return $obj;
    }

    /**
     * Deletes a new object
     *
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $obj = $this->find($id);
        Event::fire('repository.deleting', [$obj]);
        return $obj->delete();
    }

    /**
     * Find a model by his id
     *
     * @param $id
     * @return mixed
     * @throws \LaravelAcl\Authentication\Exceptions\UserNotFoundException
     */
    public function find($id)
    {
        try
        {
            $group = $this->sentry->findGroupById($id);
        }
        catch(GroupNotFoundException $e)
        {
            throw new NotFoundException;
        }

        return $group;
    }

    /**
     * Obtains all models
     *
     * @override
     * @param array $search_filters
     * @return mixed
     */
    public function all(array $search_filters = [])
    {
        $q = $this->sentry->getGroupProvider()->createModel();
        $q = $this->applySearchFilters($search_filters, $q);

        $results_per_page = $this->config_reader->get('acl_base.groups_per_page');
        return $q->paginate($results_per_page);
    }

    /**
     * @param array $search_filters
     * @param       $q
     * @return mixed
     */
    protected function applySearchFilters(array $search_filters, $q)
    {
        if(isset($search_filters['name']) && $search_filters['name'] !== '') $q = $q->where('name', 'LIKE', "%{$search_filters['name']}%");
        return $q;
    }
}