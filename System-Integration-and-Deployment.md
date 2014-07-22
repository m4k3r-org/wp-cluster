
### Getting Started
Standard Linux Makefile is used for easy setup for local development as well as building a Docker container for distribution.
All below commands should be ran from the project root.

* `make install` - Install for Development
* `make build` - Build Docker image for distribution.
* `make docker` - Create docker image.
* `make release` - Release docker image.

#### Other Commands
* `tail -f application/logs/*/*.log` - Monitor all logs.

### Working With Container (WIP)
* `docker pull  andypotanin/www.discodonniepresents.com:latest` - Pull latest Docker image.
* `docker run --name=edm -d  andypotanin/www.discodonniepresents.com:latest start` - Run Docker image as server.
* `docker run --name=dev -d -v /var/www/themes -v /var/www/plugins andypotanin/www.discodonniepresents.com:latest` - Create volumes for development.
* `docker run --name=dev -it andypotanin/www.discodonniepresents.com:latest  --help` - Get help commands.
