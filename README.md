# Tree hierarchy builder
This application exposes RESTful endpoints to:
1) Authenticate users and provide jwt tokens to secure access to the API.
2) Construct a tree representing parent -> child(ren) -> grandchild(ren) .. hierarchy from an associative array as follows: `{child: parent,..}`.

## Installation
1) Create a Docker container from the image.

        docker-compose up -d
2) Install composer.

        docker-compose exec slim sh
        curl -s https://getcomposer.org/installer | php
3) Install project dependencies.

        php composer.phar install
## Run the application

The RESTful API is avaiable at http://127.0.0.1:8090

### RESTful API endpoint

| Type | URI       | Header                     | Body                                              | Response    |
|------|-----------|----------------------------|---------------------------------------------------|-------------|
| POST | login     | -                          | JSON Object `{"username":"...","password":"..."}` | JWT         |
| POST | hierarchy | Authentication: Bearer JWT | JSON Object `{child: parent,..}`                  | JSON Object |

## Run the tests

The project has a set of unit and functional tests that can be utilized upon implementation/development to assert code functional compatibility. Run tests using

    docker-compose exec slim php composer.phar test
