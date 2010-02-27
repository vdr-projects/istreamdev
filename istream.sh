#!/bin/bash

STREAM=$1
VRATE=$2
ARATE=$3
ACHANNELS=$4
XY=$5
HTTP_PATH="$6ram/"

SEGDUR=10		# Length of Segments produced (between 10 and 30)
SEGWIN=$7		# Amount of Segments to produce 
FFPATH=$8
SEGMENTERPATH=$9
SESSION=${10}

if [ $# -eq 0 ]
then
echo "Format is : ./istream.sh source video_rate audio_rate audio_channels 480x320 httppath segments_number ffmpeg_path segmenter_path"
exit 1
fi

#############################################################
# start dumping the TS via Streamdev into a pipe for ffmpeg
# and store baseline 3.0 mpegts to outputfile  
# sending it to the segmenter via a PIPE
##############################################################

test -L ram && (test -d /dev/shm/ram || mkdir /dev/shm/ram)

cd ram/$SESSION

2> /dev/null rm stream*.ts

2> /dev/null $FFPATH -i "$STREAM" -deinterlace -f mpegts -acodec libmp3lame -ab $ARATE -ac $ACHANNELS -s $XY -vcodec libx264 -b $VRATE -flags +loop \
 -cmp \+chroma -partitions +parti4x4+partp8x8+partb8x8 -subq 5 -trellis 1 -refs 1 -coder 0 -me_range 16  -keyint_min 25 \
 -sc_threshold 40 -i_qfactor 0.71 -bt $VRATE -maxrate $VRATE -bufsize $VRATE -rc_eq 'blurCplx^(1-qComp)' -qcomp 0.6 \
 -qmin 10 -qmax 51 -qdiff 4 -level 30  -g 30 -async 2 -threads 4 - | \
$SEGMENTERPATH - $SEGDUR stream stream.m3u8 $HTTP_PATH$SESSION/ $SEGWIN &

// Save segmenterpid
echo "$!" > streamsegmenterpid
