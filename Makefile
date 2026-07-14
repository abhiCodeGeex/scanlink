up:
	docker compose up --build

down:
	docker compose down

fresh:
	docker compose down -v
	docker compose up --build

shell:
	docker compose exec app sh
