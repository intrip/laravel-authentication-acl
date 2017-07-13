<?php namespace LaravelAcl\Library\Form;
/**
 * Class FormModel
 *
 * Class to save form data associated to a model
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use LaravelAcl\Library\Validators\ValidatorInterface;
use LaravelAcl\Library\Exceptions\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\MessageBag;
use LaravelAcl\Library\Exceptions\NotFoundException;
use LaravelAcl\Authentication\Exceptions\PermissionException;
use Event;

class FormModel implements FormInterface{

    /**
     * Validator
     * @var \LaravelAcl\Library\Validators\ValidatorInterface
     */
    protected $v;
    /**
     * Repository used to handle data
     * @var
     */
    protected $r;
    /**
     * Name of the model id field
     * @var string
     */
    protected $id_field_name = "id";
    /**
     * Validaton errors
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    public function __construct(ValidatorInterface $validator, $repository)
    {
        $this->v = $validator;
        $this->r = $repository;
    }

    /**
     * Process the input and calls the repository
     * @param array $input
     * @throws \LaravelAcl\Library\Exceptions\JacopoExceptionsInterface
     */
    public function process(array $input)
    {

        if (method_exists($this->r, 'getModel')) {
            $model = $this->r->getModel();
            if ($model instanceof \Illuminate\Database\Eloquent\Model) {
                $table_name = (new $model)->getTable();
                if ($table_name) {
                    $input['_table_name'] = $table_name;
                }
            }
        }



        if($this->v->validate($input))
        {
            Event::fire("form.processing", array($input));
            return $this->callRepository($input);
        }
        else
        {
            $this->errors = $this->v->getErrors();
            throw new ValidationException;
        }
    }

    /**
     * Calls create or update depending on giving or not the id
     * @param $input
     * @throws \LaravelAcl\Library\Exceptions\NotFundException
     */
    protected function callRepository($input)
    {
        if($this->isUpdate($input))
        {
            try
            {
                $obj = $this->r->update($input[$this->id_field_name], $input);
            }
            catch(ModelNotFoundException $e)
            {
                $this->errors = new MessageBag(array("model" => "Element not found."));
                throw new NotFoundException();
            }
            catch(PermissionException $e)
            {
                $this->errors = new MessageBag(array("model" => "You don't have the permission to edit this item. Does the item is associated to other elements? if so delete the associations first."));
                throw new PermissionException();
            }
        }
        else
        {
            try
            {
                $obj = $this->r->create($input);
            }
            catch(NotFoundException $e)
            {
                $this->errors = new MessageBag(array("model" => $e->getMessage()));
                throw new NotFoundException();
            }
        }

        return $obj;
    }

    /**
     * Check if the operation is update or create
     * @param $input
     * @return booelan $update update=true create=false
     */
    protected function isUpdate($input)
    {
        return (isset($input[$this->id_field_name]) && ! empty($input[$this->id_field_name]) );
    }

    /**
     * Run delete on the repository
     * @param $input
     * @throws \LaravelAcl\Library\Exceptions\NotFoundException
     * @todo test with exceptions
     */
    public function delete(array $input)
    {
        if(isset($input[$this->id_field_name]) && ! empty($input[$this->id_field_name]))
        {
            try
            {
                $this->r->delete($input[$this->id_field_name]);
            }
            catch(ModelNotFoundException $e)
            {
                $this->errors = new MessageBag(array("model" => "Element does not exists."));
                throw new NotFoundException();
            }
            catch(PermissionException $e)
            {
                $this->errors = new MessageBag(array("model" => "Cannot delete this item, please check that the item is not already associated to any other element, in that case remove the association first."));
                throw new PermissionException();
            }
        }
        else
        {
            $this->errors = new MessageBag(array("model" => "Id not given"));
            throw new NotFoundException();
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param string $id_name
     */
    public function setIdName($id_name)
    {
        $this->id_field_name = $id_name;
    }

    /**
     * @return string
     */
    public function getIdName()
    {
        return $this->id_field_name;
    }
}