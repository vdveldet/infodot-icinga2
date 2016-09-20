#!/bin/bash

. /usr/local/bin/common

#This container will store all in the /mnt derictory so you can export this as a volume in Docker.
#Configuration and data that is stored will be moved and symlinked below

if [ -L /etc/icingaweb2 ]; then
	echo_log "/etc/icingaweb2 is a link ..."
	echo_log "Addapting ownership on /etc/icingaweb2 ..."
        chown -R apache:icingaweb2 /etc/icingaweb2/*

fi

if [ -L /etc/icinga2 ]; then
        echo_log "/etc/icinga2 is a link ..."
	echo_log "Addapting ownership on /etc/icinga2 ..."
	chown -R icinga:icingacmd /etc/icinga2/*
fi


echo_log "Enabling icingaweb2 modules."
        if [[ -L /etc/icingaweb2/enabledModules/monitoring ]]; then echo "Symlink for /etc/icingaweb2/enabledModules/monitoring exists already...skipping"; else ln -s /usr/share/icingaweb2/modules/monitoring /etc/icingaweb2/enabledModules/monitoring; fi
        if [[ -L /etc/icingaweb2/enabledModules/doc ]]; then echo "Symlink for /etc/icingaweb2/enabledModules/doc exists already...skipping"; else ln -s /usr/share/icingaweb2/modules/doc /etc/icingaweb2/enabledModules/doc; fi


echo_log "Checking for icinga2 executable ..."

if [ -x /usr/sbin/icinga2 ]; then
	echo_log "Enabling command feature ..."
	/usr/sbin/icinga2 feature enable command 
	
	echo_log "Enabling command graphite ..."
	/usr/sbin/icinga2 feature enable graphite
	if [ ! -f /etc/icinga2/pki/ca.crt ]; then
		
		echo_log "Setting up new icinga2 environment ..."
		/usr/sbin/icinga2 api setup
	else
		echo_log "Using configured environment ..."
	fi
fi
