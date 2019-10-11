<?php

namespace App\Controllers;

use App\Models\User;
use App\Validation\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController extends Controller {

    public function __construct($container) {
        parent::__construct($container);
    }

    public function index($request, $response) {
        $users = User::all();
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($users));
    }

    public function show(Request $request, Response $response, $args) {
        $id = $args['id'];

        if (v::intVal()->validate($id) == false) {
            return $response->withStatus(400)
                ->withHeader('Content-Type', 'text/html')
                ->write("Incorrect ID");;
        }

        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            return $response->withStatus(404)
                ->withHeader('Content-Type', 'text/html')
                ->write($exception->getMessage());
        }

        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($user));
    }

    public function store(Request $request, Response $response) {

        /** @var Validator $validation */
        $validation = $this->validator->validate($request, [
            'email' => v::noWhitespace()->notEmpty()->userEmailAvailable(),
            'name' => v::noWhitespace()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $response->withStatus(400)
                ->withHeader('Content-Type', 'text/html')
                ->write(json_encode($validation->getErrors()));
        }

        $data = $request->getParsedBody();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email']
        ]);
        $user->save();

        return $response->withStatus(201)
            ->withHeader('Content-Type', 'text/html')
            ->withHeader('Location', APP_URL . ':' . $_SERVER['SERVER_PORT'] . '/api/users/' . $user->id);

    }

    public function update(Request $request, Response $response, $args) {
        $id = $args['id'];

        if (v::intVal()->validate($id) == false) {
            return $response->withStatus(400);
        }

        try {
            $user = User::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            return $response->withStatus(404)
                ->withHeader('Content-Type', 'text/html')
                ->write($exception->getMessage());
        }

        /** @var Validator $validation */
        $validation = $this->validator->validate($request, [
            'email' => v::noWhitespace()->notEmpty(),
            'name' => v::noWhitespace()->notEmpty(),
        ]);


        if ($isEmailAlreadyExists = $this->checkUniqueEmail($request->getParam('email'), $id)) {
            return $response->withStatus(400)
                ->withHeader('Content-Type', 'text/html')
                ->write("Cet email existe déjà");
        }

        if ($validation->failed() || $isEmailAlreadyExists) {
            return $response->withStatus(400)
                ->withHeader('Content-Type', 'text/html')
                ->write(json_encode($validation->getErrors()));
        }

        $data = $request->getParsedBody();

        $user->update([
            'email' => $data['email'] ?? $user->email,
            'name' => $data['name'] ?? $user->name,
        ]);

        return $response->withStatus(200);
    }

    public function destroy(Request $request, Response $response, $args) {
        $id = $args['id'];
        if (v::intVal()->validate($id) == false) {
            return $response->withStatus(400);
        }
        User::destroy($id);
        return $response->withStatus(204);
    }

    private function checkUniqueEmail($email, $id) {
        return (User::where('id', '!=', $id)
            ->where('email', $email)
            ->get())->isNotEmpty();
    }

}