<?php

namespace App\Repositories\Eloquent;
;
use App\Models\Project;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Support\Arr;
use App\Exceptions\GeneralException;
use App\Repositories\Contracts\ProjectsRepository;
use Throwable;


/**
 * Class EloquentProjectsRepository
 */
class EloquentProjectsRepository extends EloquentBaseRepository implements ProjectsRepository
{


    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * EloquentCustomerRepository constructor.
     *
     * @param  Project  $project
     * @param  Repository  $config
     */
    public function __construct(Project $project, Repository $config)
    {
        parent::__construct($project);
        $this->config = $config;
    }

    /**
     * @param  array  $input
     * @param  bool  $confirmed
     *
     * @return Project
     * @throws GeneralException
     *
     */
    public function store(array $input): Project
    {

        /** @var Project $project */
        $project = $this->make(Arr::only($input, ['name', 'description']));

        if (!$this->save($project, $input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $project;
    }


    /**
     * @param  Project  $project
     * @param  array  $input
     *
     * @return Project|mixed
     * @throws GeneralException
     *
     */
    public function update(Project $project, array $input): Project
    {

        $project->fill(Arr::except($input, 'password'));

        if (!$this->save($project, $input)) {
            throw new GeneralException(__('locale.exceptions.something_went_wrong'));
        }

        return $project;
    }

    /**
     * @param  Project  $project
     * @param  array  $input
     *
     * @return bool
     */
    private function save(Project $project, array $input): bool
    {
        if (!$project->save()) {
            return false;
        }

        return true;
    }

    /**
     * @param  Project  $project
     *
     * @return bool|null
     * @throws Exception|Throwable
     *
     */
    public function destroy(Project $project)
    {
        if (!$project->can_delete) {
            throw new GeneralException(__('exceptions.backend.projects.first_project_cannot_be_destroyed'));
        }

        if (!$project->delete()) {
            throw new GeneralException(__('exceptions.backend.projects.delete'));
        }

        return true;
    }
}
