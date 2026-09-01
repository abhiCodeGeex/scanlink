---
name: scanlink-running-tests
description: "git/scanlink has no vendor/, so tests run by copying the source into a throwaway container with the shared vendor volume"
metadata: 
  node_type: memory
  type: project
  originSessionId: 6d31b29a-a11d-4965-817d-304291c7082e
  modified: 2026-09-01T05:05:57.199Z
---

`E:\abhishek-project\git\scanlink` has **no `vendor/`**, so PHPUnit cannot run there directly. `composer.json` and `composer.lock` are byte-identical to the `scanlink-laravel` checkout, whose dependencies live in the Docker volume `scanlink-laravel_vendor_data`.

Working recipe (copy the source **into** the container — a Windows bind mount hangs for minutes and produces no output):

```bash
docker run -d --name swmstest -v scanlink-laravel_vendor_data:/vendor_src:ro -w /app scanlink-laravel-app sleep 1800
tar -cf - --exclude=.git --exclude=node_modules app bootstrap config database resources routes tests artisan composer.json composer.lock phpunit.xml storage public | docker exec -i swmstest sh -c 'mkdir -p /app && tar -xf - -C /app'
docker exec swmstest sh -lc 'cp -a /vendor_src /app/vendor'
docker exec -e APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= swmstest sh -lc 'cd /app && php vendor/bin/phpunit --filter Foo --no-coverage'
```

Git Bash mangles container paths — prefix docker commands with `MSYS_NO_PATHCONV=1`. Tests use sqlite `:memory:` (phpunit.xml), so the live Docker MySQL is never touched.

**Why:** without this there is no way to run the suite at all, and the obvious approach (bind mount) silently hangs.

**How to apply:** rebuild the container after editing files, or `docker exec -i ... cat > /app/<path>` single files. See [[scanlink-test-baseline]] before judging failures.
