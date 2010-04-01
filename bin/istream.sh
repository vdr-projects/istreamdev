#!/bin/bash

STREAM=$1
VRATE=$2
ARATE=$3
XY=$4
HTTP_PATH="$5ram/"

SEGDUR=10		# Length of Segments produced (between 10 and 30)
SEGWIN=$6		# Amount of Segments to produce 
FFPATH=$7
SEGMENTERPATH=$8
SESSION=${9}
FFMPEGLOG=${10}

if [ $# -eq 0 ]
then
echo "Format is : ./istream.sh source video_rate audio_rate audio_channels 480x320 httppath segments_number ffmpeg_path segmenter_path"
exit 1
fi

# Log
if [ -z "$FFMPEGLOG" ]
then
	FFMPEGLOG="/dev/null"
fi

#############################################################
# start dumping the TS via Streamdev into a pipe for ffmpeg
# and store baseline 3.0 mpegts to outputfile  
# sending it to the segmenter via a PIPE
##############################################################

# Check that the session dir exists
if [ ! -e ../ram/$SESSION ]
then
	exit;
fi

cd ../ram/$SESSION

# Create a fifo
2>/dev/null mkfifo ./fifo

#(trap "rm -rf $TMP; /usr/local/bin/fw" EXIT HUP INT TERM ABRT; cat $TMP/$OUT) &

# Start ffmpeg
(trap "rm -f ./ffmpeg.pid; rm -f ./fifo" EXIT HUP INT TERM ABRT; $FFPATH -i "$STREAM" -deinterlace -f mpegts -acodec libmp3lame -ab $ARATE -ac 2 -s $XY -vcodec libx264 -b $VRATE -flags +loop \
 -cmp \+chroma -partitions +parti4x4+partp8x8+partb8x8 -subq 5 -trellis 1 -refs 1 -coder 0 -me_range 16  -keyint_min 25 \
 -sc_threshold 40 -i_qfactor 0.71 -bt $VRATE -maxrate $VRATE -bufsize $VRATE -rc_eq 'blurCplx^(1-qComp)' -qcomp 0.6 \
 -qmin 10 -qmax 51 -qdiff 4 -level 30  -g 30 -async 2 -threads 4 - 2>$FFMPEGLOG > ./fifo) &

# Store ffmpeg pid
PID=$!
2>/dev/null echo `\ps ax --format pid,ppid | grep "$PID$" | awk {'print $1'}` > ./ffmpeg.pid

# Now start segmenter
(trap "rm -f ./segmenter.pid" EXIT HUP INT TERM ABRT; 2>/dev/null $SEGMENTERPATH ./fifo $SEGDUR stream stream.m3u8 $HTTP_PATH$SESSION/ $SEGWIN) &

# Store segmenter pid
PID=$!
2>/dev/null echo `\ps ax --format pid,ppid | grep "$PID$" | awk {'print $1'}` > ./segmenter.pid

