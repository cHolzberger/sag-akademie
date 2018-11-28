#!/usr/bin/env powershell

. .\vars.ps1

&"docker-compose" $dc_args "config"

"$env:BUILD_BRANCH" | Out-File  ../sag-akademie.de/env -NoNewline -Encoding ascii
