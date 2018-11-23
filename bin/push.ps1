. .\vars.ps1

&"docker-compose.exe" $dc_args "config"
&"docker-compose.exe" $dc_args "push"
