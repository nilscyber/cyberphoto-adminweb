#!/bin/sh
cd /etc/pki/java
#/usr/lib/jvm/jre-1.7.0/bin/java -cp /home/phplib/swish/swish.jar swish.Swish "$1" "$2" "$3" "$4" "$5" "$6" "$7" "$8" "$9" "${10}" "${11}" "${12}" "${13}" "${14}"
#/usr/lib/jvm/jre-1.8.0/bin/java -cp /home/phplib/swish/swish_v2.jar swish.Swish "$1" "$2" "$3" "$4" "$5" "$6" "$7" "$8" "$9" "${10}" "${11}" "${12}" "${13}" "${14}"
/usr/lib/jvm/jre-1.8.0/bin/java -Dhttps.protocols=TLSv1.2 -Djdk.tls.client.protocols=TLSv1.2 -Djavax.net.debug=ssl:handshake -cp /home/phplib/swish/swish_v3.jar swish.Swish "$1" "$2" "$3" "$4" "$5" "$6" "$7" "$8" "$9" "${10}" "${11}" "${12}" "${13}" "${14}"