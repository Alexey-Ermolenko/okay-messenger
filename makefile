docker-build:
	docker build -t localhost ./.docker; \
	docker compose up --build -d; \

make docker-up:
	docker compose up -d

make docker-stop:
	docker compose stop

make docker-exec:
	docker exec -it php /bin/bash