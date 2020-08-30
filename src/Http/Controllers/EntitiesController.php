<?php namespace Helium\Http\Controllers;

use Helium\Support\Entity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntitiesController extends Controller
{

    /**
     * Lists entities using a table builder
     *
     * @return void
     */
    public function index(string $entityType)
    {
        $entity = $this->getEntity($entityType);
        dd('index');
    }

    /**
     * Edit an entity using a form builder
     *
     * @param string $entity The entity type
     * @param int $id The entity id
     * @return void
     */
    public function edit(Request $request, string $entityType, int $id)
    {
        $entity = $this->getEntity($entityType);

        if ($request->isMethod('post')) {
            $entity->getFormHandler()
                ->validate($request->all())
                ->post($request->all());

            return back()->with('message', [
                'type' => 'success',
                'message' => 'Saved successfully'
            ]);
        }

        $form = $entity->getFormBuilder()
            ->setInstance($entity->getRepository()->find($id))
            ->getForm();

        return view('helium::form', [
            'form' => $form
        ]);
    }

    /**
     * Gets an Entity by its slug
     *
     * @param string $slug
     * @return Entity
     */
    protected function getEntity(string $slug) : Entity {
        $entityClass = config('helium.entities.' . $slug);

        if (!$entityClass || !class_exists($entityClass)) {
            throw new NotFoundHttpException();
        }

        return app()->make($entityClass);
    }
}
