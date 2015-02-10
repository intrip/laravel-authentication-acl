<?php namespace Jacopo\Library\Repository\Interfaces;
/**
 * Interface BaseRepositoryInterface
 *
 * @author jacopo beschi j.beschi@jacopo.com
 */
interface BaseRepositoryInterface 
{
    /**
     * Create a new object
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update a new object
     * @param id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * Deletes a new object
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * Find a model by his id
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Obtains all models
     * @return mixed
     */
    public function all();
} 