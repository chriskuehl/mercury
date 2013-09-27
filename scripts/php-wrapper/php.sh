#!/bin/bash
# HTTPS fix (WARNING: THIS HEADER CAN BE FAKED BY THE CLIENT)
if [[ "$HTTP_X_FORWARDED_PROTO" == "https" ]]; then
	export SERVER_PORT=443
	export HTTPS="on"
else
	export SERVER_PORT=80
fi

# client address fix (WARNING: THIS HEADER CAN BE FAKED BY THE CLIENT)
# takes the first part of X-Forwared-For and uses it for REMOTE_ADDR
REMOTE_ADDR=$(echo "$HTTP_X_FORWARDED_FOR" | sed -e 's/,.*//g')

# clean up some headers
unset HTTP_X_FORWARDED_FOR
unset HTTP_X_FORWARDED_PROTO

php-cgi --define "open_basedir=/home/$(whoami)/$(whoami)/"
