#!/usr/bin/env powershell

. .\vars.ps1

&"docker-compose" $dc_args "up" $args
