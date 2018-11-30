#!/usr/bin/env powershell

$env:BUILD_VERSION="20181128"
$env:BUILD_VERSION_WEB=$env:BUILD_VERSION
$env:BUILD_VERSION_MYSQL=$env:BUILD_VERSION
$env:BUILD_BRANCH="dev"
$env:RELEASE_BRANCH="dev"

$env:PROJECT_HOME="c:/projekte/sag-new"
$dc_args = @("-p", $env:RELEASE_BRANCH,
"-f", "../docker-compose.yml", 
"-f", "../docker-compose/build-$env:BUILD_BRANCH.yml",
"-f", "../docker-compose/vol-$env:BUILD_BRANCH.yml")
