# Gopposs Laravel Backend API

# Docker Setup for gopposs-be

To build and run your Docker container for the `gopposs-be` application, follow the steps below.

### Building the Docker Image

First, build the Docker image using the following command:

```sh
docker build -t gopposs-be .
```

### Running the Docker Container

Run the Docker container in HTTP mode:

```sh
docker run -p 8000:8000 --env-file .env --rm gopposs-be
```

### Additional Docker Run Options

You have various options to run your Docker container in different modes:

#### HTTP Mode

```sh
docker run -p <port>:8000 --rm <image-name>:<tag>
```

#### Horizon Mode

```sh
docker run -e CONTAINER_MODE=horizon --rm <image-name>:<tag>
```

#### Scheduler Mode

```sh
docker run -e CONTAINER_MODE=scheduler --rm <image-name>:<tag>
```

#### HTTP Mode with Horizon

```sh
docker run -e WITH_HORIZON=true -p <port>:8000 --rm <image-name>:<tag>
```

#### HTTP Mode with Scheduler

```sh
docker run -e WITH_SCHEDULER=true -p <port>:8000 --rm <image-name>:<tag>
```

#### HTTP Mode with Scheduler and Horizon

```sh
docker run -e WITH_SCHEDULER=true -e WITH_HORIZON=true -p <port>:8000 --rm <image-name>:<tag>
```

#### Worker Mode

```sh
docker run -e CONTAINER_MODE=worker -e WORKER_COMMAND="php /var/www/html/artisan foo:bar" --rm <image-name>:<tag>
```

#### Running a Single Command

```sh
docker run --rm <image-name>:<tag> php artisan about
```

Replace `<port>`, `<image-name>`, and `<tag>` with the appropriate values for your setup.
