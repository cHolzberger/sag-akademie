. .\vars.ps1

&"docker-compose.exe" $dc_args "config"

"$env:BUILD_BRANCH" | Out-File  ../sag-akademie.de/env -NoNewline -Encoding ascii
