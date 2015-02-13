<?php  namespace Jacopo\Authentication\Tests\Unit\Stubs; 

use Jacopo\Library\Repository\EloquentBaseRepository;

class VoidRepository extends EloquentBaseRepository{
    public function __construct(){}
    public function create(array $data){}
    public function update($id, array $data){}
    public function delete($id){}
    public function find($id){}
    public function all(){}
} 