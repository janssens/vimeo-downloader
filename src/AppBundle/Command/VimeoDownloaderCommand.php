<?php
// src/AppBundle/Command/Txt2ImgCommand.php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;

use FFMpeg;
use DOMDocument;
use DOMNode;

class VimeoDownloaderCommand extends Command
{
    private $_output;
    private $_input;

	protected function configure()
	{
		$this
		->setName('vimeoDownloader')
		->setDescription('Download Vimeo video')
		->setHelp("This command allows you to download video from vimeo...")
		->addArgument('video_files_directory', InputArgument::REQUIRED, 'directory of m4s video files')
		->addArgument('audio_files_directory', InputArgument::REQUIRED, 'directory of m4s audio files')
		->addOption('first_index','f',InputOption::VALUE_OPTIONAL,'The first index, default is 1','1')
		->addOption('last_index','l',InputOption::VALUE_OPTIONAL,'The last index, default is 500','500')
		->addOption('segment_name_template','r',InputOption::VALUE_OPTIONAL,'The template, default is segment-{{index}}.m4s','segment-{{index}}.m4s')
		->addOption('output_dir','d',InputOption::VALUE_OPTIONAL,'Output dir','');
//		->addOption('encoding','e',InputOption::VALUE_OPTIONAL,'Encoding','UTF-8')
//		->addOption('fontSizeMax',NULL,InputOption::VALUE_OPTIONAL,'Max font size','')
//		->addOption('wrap','w',InputOption::VALUE_NONE,'should we wrap')
//		->addOption('fit',NULL,InputOption::VALUE_NONE,'fit output to background-image');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
        $this->_output = $output;
        $this->_input = $input;
	    //https://vars.hotjar.com/rcj-99d43ead6bdf30da8ed5ffcb4f17100c.html
        //https://skyfire.vimeocdn.com/1490950226-0xc5589d4c92206f3cf0232c58e3cc46efe9e1d847/207989456/sep/audio/710172611/chop/segment-36.m4s
		$default_params = array(
			'font-family' => "Times",
        );

	    if ($this->isVerbose())
            $this->_output->writeln("finale output is w".$this->_width." and h".$this->_height);

        $first_index = intval($this->_input->getOption('first_index'));
        $last_index = intval($this->_input->getOption('last_index'));

        if ($last_index < $first_index)
            die();

        $video_files_directory = $this->_input->getArgument('video_files_directory');
        $audio_files_directory = $this->_input->getArgument('audio_files_directory');
        $output_dir = $this->_input->getOption('output_dir');
        $segment_name_template = $this->_input->getOption('segment_name_template');

        $progress = new ProgressBar($this->_output, $last_index-$first_index);

        $video_files = array();
        $audio_files = array();
        $files = array();

        mkdir($output_dir.DIRECTORY_SEPARATOR."audio");
        mkdir($output_dir.DIRECTORY_SEPARATOR."video");

        $final_video_output = $output_dir.DIRECTORY_SEPARATOR.'video.mp4';
        $final_audio_output = $output_dir.DIRECTORY_SEPARATOR.'audio.mp4';

        $filename = str_replace('{{index}}','0',$segment_name_template);
        $filename = str_replace('m4s','mp4',$filename);
        $content = file_get_contents($video_files_directory.$filename);
        file_put_contents($final_video_output,$content);
        $content = file_get_contents($audio_files_directory.$filename);
        file_put_contents($final_audio_output,$content);

        for ($i = $first_index; $i <= $last_index; $i++){
            $filename = str_replace('{{index}}',$i,$segment_name_template);
            $local_filename = str_replace('{{index}}',sprintf("%04d", $i),$segment_name_template);
            $content = file_get_contents($video_files_directory.$filename);
            $output_path = $output_dir.DIRECTORY_SEPARATOR.'video'.DIRECTORY_SEPARATOR.$local_filename;
            file_put_contents($output_path, $content);
            file_put_contents($final_video_output, $content,FILE_APPEND);
            $video_files[] = $output_path;
            $files[] = $output_path;
            $content = file_get_contents($audio_files_directory.$filename);
            $output_path = $output_dir.DIRECTORY_SEPARATOR.'audio'.DIRECTORY_SEPARATOR.$local_filename;
            file_put_contents($final_audio_output, $content,FILE_APPEND);
            file_put_contents($output_path, $content);
            $audio_files[] = $output_path;
            $files[] = $output_path;
            $progress->advance();
        }

        $progress->finish();

//        $ffmpeg = FFMpeg\FFMpeg::create();
//        $video = $ffmpeg->open($files[0]);
//        $video
//            ->concat($files)
//            ->saveFromSameCodecs($dir.DIRECTORY_SEPARATOR.'result.mp4', TRUE);

//        cp init.mp4 output.mp4
//        cat *.m4s >> output.mp4


//        $ffmpeg = FFMpeg\FFMpeg::create();
//        $video = $ffmpeg->open('video.mpg');
//        $video
//            ->filters()
//            ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
//            ->synchronize();
//        $video
//            ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
//            ->save('frame.jpg');
//        $video
//            ->save(new FFMpeg\Format\Video\X264(), 'export-x264.mp4')
//            ->save(new FFMpeg\Format\Video\WMV(), 'export-wmv.wmv')
//            ->save(new FFMpeg\Format\Video\WebM(), 'export-webm.webm');

    }


    protected function isVerbose(){
        return $this->_input->getOption('verbose');
    }
}