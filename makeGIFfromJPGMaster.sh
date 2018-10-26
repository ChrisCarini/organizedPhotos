#!/bin/bash

MYSQL_USER=username
MYSQL_PASS=password
MYSQL_HOST=db.hostname.com
MYSQL_DB=db_name

if [ $# -lt 2 ]
then
	printf "Usage:\n\t./makeGIFfromJPGMaster.sh <folder year> <new size in px>\n"
	exit 0
else
	if [ -e 'makeGIFfromJPGMaster.sh.lockfile' ]
	then
		printf "Another process must be running this script; lock file exists.\nNow exiting...\n"
	else
		touch makeGIFfromJPGMaster.sh.lockfile # make lock file
		fCount=0;
		cCount=0;
		res1=$(date +%s.%N)

		for i in $(find ./$1  -type f -name *JPG);
		do
			#echo $i 
			#j=`echo $i | cut -d . -f 2`; # old way, only worked w/ 1 '.' in filename
			j=`echo "${i%.*}"`;
			#j="."$j;
			#echo "$j"
			j=$j"_$2px.gif";
			#echo "$j"

			if [ -e $j ]
			then
				#echo "EXISTS...Do Nothing."
				let fCount=fCount+1
			else
				echo "Make GIF from $i !!!!"
				convert $i -resize $2 $j ;

				# Add row to MySQL Database sql file for import
				album=`echo $i | grep -o ....-..-..*./ | cut -d '/' -f 1`;
				parseddate=`echo $album | grep -o ....-..-..`;
				y=`echo $parseddate | cut -d '-' -f 1`;
				m=`echo $parseddate | cut -d '-' -f 2`;
				d=`echo $parseddate | cut -d '-' -f 3`;
				echo "INSERT INTO \`$MYSQL_DB\`.\`photos\` (\`id\`,\`fullPath\`,\`album\`,\`display\`,\`exifData\`,\`year\`,\`month\`,\`day\`) VALUES (NULL, '$i', '$album', '0', '0', '$y', '$m', '$d');" >> importNewPhotosToMySQL.sql
				let cCount=cCount+1
			fi
		done

		if [ -e 'importNewPhotosToMySQL.sql' ]
			then
				echo "Import sql file into MySQL Database..."
				echo "EXIT" >> importNewPhotosToMySQL.sql
				# Import sql file into MySQL Database
				mysql -u ${MYSQL_USER} --password="$MYSQL_PASS" -h ${MYSQL_HOST} ${MYSQL_DB} < importNewPhotosToMySQL.sql
				rm importNewPhotosToMySQL.sql
				echo "Done."
		fi

		res2=$(date +%s.%N)
		#echo "Start time: $res1"
		#echo "Stop time:  $res2"
		printf "Finshed folder %s - converted to %spx\n" $1 $2
		printf "Elapsed time:    %.3F seconds\n"  $(echo "$res2 - $res1"|bc )
		echo "Total converted images: $cCount";
		echo "Total file count: $fCount";

		rm makeGIFfromJPGMaster.sh.lockfile # make lock file
	fi
fi
exit
