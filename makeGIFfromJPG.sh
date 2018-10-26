#!/bin/bash

res1=$(date +%s.%N)
if [ -e 'makeGIFfromJPG.sh.lockfile' ]
then
	printf "Another process must be running this script; lock file exists.\nNow exiting...\n"
else
	touch makeGIFfromJPG.sh.lockfile # make lock file
	printf "Making 400px GIFs...\n"
	sh ./makeGIFfromJPGMaster.sh 2010 400
	sh ./makeGIFfromJPGMaster.sh 2011 400
	sh ./makeGIFfromJPGMaster.sh 2012 400
	sh ./makeGIFfromJPGMaster.sh 2013 400
	sh ./makeGIFfromJPGMaster.sh 2014 400
	sh ./makeGIFfromJPGMaster.sh 2015 400
	sh ./makeGIFfromJPGMaster.sh 2016 400
	printf "Done making 400px GIFs.\n\n"

	printf "Making 800px GIFs...\n"
	sh ./makeGIFfromJPGMaster.sh 2010 800
	sh ./makeGIFfromJPGMaster.sh 2011 800
	sh ./makeGIFfromJPGMaster.sh 2012 800
	sh ./makeGIFfromJPGMaster.sh 2013 800
	sh ./makeGIFfromJPGMaster.sh 2014 800
	sh ./makeGIFfromJPGMaster.sh 2015 800
	sh ./makeGIFfromJPGMaster.sh 2016 800
	printf "Done making 800px GIFs.\n\n"

	printf "Script complete!\n"
	rm makeGIFfromJPG.sh.lockfile # remove lock file
fi
res2=$(date +%s.%N)
#echo "Start time: $res1"
#echo "Stop time:  $res2"
printf "Elapsed time:    %.3F seconds\n"  $(echo "$res2 - $res1"|bc )

exit
