#!/bin/bash



LOCALPATH='/var/www/html/owncloud/data/'$1'/files/'$2'/input/'

HOST=ftp-private.ncbi.nlm.nih.gov
USER=mothur
PASSWD=EUxzMrkL

ftp -inv $HOST <<EOT
quote USER $USER
quote PASS $PASSWD
cd submit/Test/bsimetathai

lcd $LOCALPATH
mput *.fastq


quit
EOT
exit 0


# put submission.xml
# put submit.ready

# options=("$@")
# tLen=${#options[@]}
# chknum=$(( ${tLen#0} -2))
# submission_xml=${options[${#options[@]}-2]}
# submit_ready=${options[${#options[@]}-1]}

# for (( i=0; i<${tLen}; i++ ));
# do
# 	if (($i < $chknum )); then
# 		fastq=(${options[$i]})
		
# 		ftp "$fastq"
		
# 	fi
  
# done 













