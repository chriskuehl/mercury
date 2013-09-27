#!/bin/bash
echo $(date)

ls /home/ | \

while read START_USER; do
	# get to a safe directory
	cd /home/$START_USER/$START_USER/
	
	if [ -f "up.sh" ];
	then
    		echo "Executing up.sh for $START_USER"
		sudo -i -u "$START_USER" "./up.sh"
	else
		echo "No up.sh exists for $START_USER"
	fi
done
