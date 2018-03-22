# Vimeo download

## Require
- [Composer](https://getcomposer.org/)
- Php 5.6+
- ffmpeg (not needed to download but to merge audio and video)

## installation
	composer install

## usage
	sh vimeoDownloader.sh -h

## merging result
ffmpeg command to merge audio and video

	ffmpeg -i audio.mp4 -i video.mp4 -acodec copy -vcodec copy out.mp4
