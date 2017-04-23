ENVIRONMENT ?= production

.PHONY : all setup build clean

all : setup build

setup :
ifeq ($(ENVIRONMENT),production)
	@npm install --quiet --progress=false > /dev/null
else
	@npm install --progress=true
endif

build : clean
	@npm run $(ENVIRONMENT)

clean :
	@rm -Rf public/*
	@touch public/.gitkeep
