all:
	gcc -Wall -g segmenter.c -o segmenter -lavformat -lavcodec -lavutil -lbz2

clean:
	rm segmenter

install: segmenter
	cp segmenter /usr/bin/

uninstall:
	rm /usr/bin/segmenter
