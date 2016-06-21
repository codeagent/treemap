<?php
namespace codeagent\treemap\site;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Faker\Generator;
use Faker\Factory;

class FilesystemCommand extends Command
{
    public static $dir   = 'data';
    public static $depth = 2;
    public static $root  = '.';
    /**
     * @var Generator
     */
    protected $generator;

    public function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->generator = Factory::create();
    }

    public function configure()
    {
        $this
            ->setName('generate:filesystem')
            ->setDescription('Generates hierarchical data structure based on the local filesystem')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of generated file', 'filesystem.php')
            ->addOption('dir', 'd', InputOption::VALUE_REQUIRED, 'Destination path where generated data will be placed', static::$dir)
            ->addOption('root', 'r', InputOption::VALUE_REQUIRED, 'Filesystem root', static::$root)
            ->addOption('depth', null, InputOption::VALUE_REQUIRED, 'Max tree depth', static::$depth);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name     = $input->getArgument('name');
        $dir      = $input->getOption('dir');
        $depth    = $input->getOption('depth');
        $root     = $input->getOption('root');
        $data     = $this->walkFilesystem($root, $depth);
        $filename = $dir . DIRECTORY_SEPARATOR . $name;
        file_put_contents($filename, "<?php return " . var_export($data, true) . ";");
        $filename = realpath($filename);
        $output->writeln("<info>Writed to {$filename}</info>");
    }

    protected function walkFilesystem($root, $depth)
    {
        $items = [];
        if($depth <= 0 || !is_dir($root)) {
            return $items;
        }
        $directory = new \DirectoryIterator($root);
        foreach($directory as $file) {
            try {
                if(!$file->isDot()) {
                    $node = [
                        'name'     => $file->getBasename(),
                        'size'     => $this->size($file, 64),
                        'modified' => $file->getMTime(),
                        'isDir'    => $file->isDir()
                    ];
                    if($file->isDir())
                        $node['files'] = $this->walkFilesystem($file->getRealPath(), $depth - 1);
                    $items[] = $node;
                }
            } catch(\RuntimeException $e) {
            }
        }
        return $items;
    }

    protected function size(\SplFileInfo $file, $depth = 5)
    {
        if($depth <= 0)
            return 0;
        if($file->isFile())
            return $file->getSize();
        $directory = new \DirectoryIterator($file->getRealPath());
        try {
            $size = 0.0;
            foreach($directory as $file) {
                if(!$file->isDot())
                    $size += $this->size($file, $depth - 1);
            }
        } catch(\RuntimeException $e) {
            $size = 0.0;
        }
        return $size;
    }
}
