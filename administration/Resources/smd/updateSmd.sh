#!/bin/zsh 

BASE="http://sag-akademie.localhost"
USER="admin"
PASSWORD=trust

wget "$BASE/_rpc/?s=JsonRpc&m=smd&t=Pages.Startseite&username=$USER&password=$PASSWORD"
