<?php namespace Riari\Forum\Repositories;

abstract class BaseRepository
{

    protected $model;
    protected $perPage;

    public function byID($id)
    {
        $this->model = $this->model->where('id', '=', $id);
        return $this;
    }

    public function get($options = array())
    {
        $options += [
            'paginated'     => true
        ];

        if ($options['paginated'])
        {
            $this->model = $this->model->paginate($this->pageLimit);
        }

        return $this->model;
    }

}
