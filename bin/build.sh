source ./vars.sh

docker-compose -p $RELEASE_BRANCH -f "../docker-compose.yml" -f "../docker-compose/build-$BUILD_BRANCH.yml" -f "../docker-compose/vol-$BUILD_BRANCH.yml" config
docker-compose -p $RELEASE_BRANCH -f "../docker-compose.yml" -f "../docker-compose/build-$BUILD_BRANCH.yml" -f "../docker-compose/vol-$BUILD_BRANCH.yml" build

echo "$BUILD_BRANCH" > ../sag-akademie.de/env
