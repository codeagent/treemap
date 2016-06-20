<?php
namespace codeagent\treemap\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class AssetsCommand extends Command
{
    public static $assetsPath = __DIR__ . "/../src/assets";

    public static $assets = [
        'css'   => "treemap.css",
        'fonts' => "OpenSans-Regular.ttf"
    ];

    public function configure()
    {
        $assets   = array_keys(static::$assets);
        $assets[] = 'all';
        $this
            ->setName('assets')
            ->setDescription('Copy treemap assets into the target dir')
            ->addArgument('path', InputArgument::REQUIRED, 'Target path')
            ->addArgument('assets', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Assets to copy. Allowed values: ' . implode(", ", $assets), []);
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {

        $dir = $input->getArgument('path');
        if(!file_exists($dir))
            mkdir($dir, 0777, true);

        $assets = $input->getArgument('assets');

        if(empty($assets))
            $assets = static::$assets;
        else
            $assets = array_intersect_key(static::$assets, array_flip($assets));

        foreach($assets as $asset) {
            copy(static::$assetsPath . "/{$asset}", "{$dir}/{$asset}");
        }

    }

}