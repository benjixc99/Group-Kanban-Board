<?php

namespace App\Repositories\Contracts;

use App\Models\Project;

/**
 * Interface ProjectsRepository.
 */
interface ProjectsRepository extends BaseRepository
{

    /**
     * @param  array  $input
     *
     * @return mixed
     */
    public function store(array $input);

    /**
     * @param  Project  $project
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(Project $project, array $input);

    /**
     * @param  Project  $project
     *
     * @return mixed
     */
    public function destroy(Project $project);
}
