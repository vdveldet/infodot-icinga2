#!/bin/bash
if [ -x /usr/sbin/icinga2 ]; then
	/usr/sbin/icinga2 feature enable command 
	if [ ! -f /etc/icinga2/pki/ca.crt ]; then
		/usr/sbin/icinga2 api setup
	fi
fi
