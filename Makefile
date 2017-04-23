ENVIRONMENT ?= production

.PHONY : all setup build clean distclean

all : setup build

setup :
ifeq ($(ENVIRONMENT),production)
	@npm install --quiet --progress=false > /dev/null
	@composer install --no-ansi --no-interaction --no-progress --optimize-autoloader --quiet --profile --prefer-dist --no-dev --no-suggest
else
	@npm install --progress=true
	@composer install --profile --optimize-autoloader
endif

build : clean
	@echo "Compiling static assets..."
	@npm run $(ENVIRONMENT)
	@php build.php

clean :
	@rm -Rf public/*
	@touch public/.gitkeep

distclean : clean
	@rm -Rf node_modules vendor
