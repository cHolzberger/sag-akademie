export BUILD_VERSION="20181115"
export BUILD_BRANCH="beta"
export RELEASE_BRANCH="beta" # dev, test, prod

docker-compose -p $RELEASE_BRANCH -f "../docker-compose.yml" -f "../docker-compose/build-$BUILD_BRANCH.yml" -f "../docker-compose/vol-$BUILD_BRANCH.yml" config

echo -n "$BUILD_BRANCH" > ../sag-akademie.de/env
