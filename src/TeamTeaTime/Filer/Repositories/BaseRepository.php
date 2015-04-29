<?php namespace Riari\Forum\Repositories;

abstract class BaseRepository
{

    protected $model;
    protected $perPage;

    public function getByID($id)
    {
        $this->model = $this->model->findOrFail($id);
        return $this;
    }

    public function get($options = array())
    {
        $options += [
            'paginated'     => false
        ];

        if ($options['paginated'])
        {
            $this->model = $this->model->paginate($this->pageLimit);
        }

        return $this->model;
    }

}
