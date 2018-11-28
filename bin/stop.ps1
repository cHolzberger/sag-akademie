#!/usr/bin/env powershell

. ./vars.ps1 

&"docker-compose" $dc_args "config"
&"docker-compose" $dc_args "down"
