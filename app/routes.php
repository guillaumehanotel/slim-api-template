<?php

// Home
$app->get("/", "HomeController:index")->setName('home');

// Users Endpoints
$app->get("/api/users", "UserController:index")->setName('users.index');
$app->get("/api/users/{id}", "UserController:show")->setName('users.show');
$app->post("/api/users", "UserController:store")->setName('users.store');
$app->put("/api/users/{id}", "UserController:update")->setName('users.update');
$app->delete("/api/users/{id}", "UserController:destroy")->setName('users.destroy');