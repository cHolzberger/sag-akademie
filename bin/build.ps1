#!/usr/bin/env powershell

. .\vars.ps1
Write-Host "Building" $env:RELEASE_BRANCH

&"docker-compose" $dc_args "config"
&"docker-compose" $dc_args "build"

"$env:BUILD_BRANCH" | Out-File  ../sag-akademie.de/env -NoNewline -Encoding ascii
"$env:BUILD_VERSION" | Out-File  ../sag-akademie.de/version -NoNewline -Encoding ascii
