. .\vars.ps1
Write-Host "Building" $env:RELEASE_BRANCH

&"docker-compose.exe" $dc_args "config"
&"docker-compose.exe" $dc_args "build"

"$env:BUILD_BRANCH" | Out-File  ../sag-akademie.de/env -NoNewline -Encoding ascii
