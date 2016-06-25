#!/bin/bash
if[ -x /usr/sbin/icinga2 ]; then
	/usr/sbin/icinga2 feature enable command 
fi
