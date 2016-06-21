<?php
namespace codeagent\treemap\site;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Faker\Generator;
use Faker\Factory;

class FakeCommand extends Command
{
    public static $dir         = 'data';
    public static $depth       = 3;
    public static $minChildren = 4;
    public static $maxChildren = 16;
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
            ->setName('generate:fake')
            ->setDescription('Generates hierarchical data structure based on faker library')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of generated file', 'data.php')
            ->addOption('dir', 'd', InputOption::VALUE_REQUIRED, 'Destination path where generated data will be placed', static::$dir)
            ->addOption('min', null, InputOption::VALUE_REQUIRED, 'Minimum children nodes to generate for parent', static::$minChildren)
            ->addOption('max', null, InputOption::VALUE_REQUIRED, 'Minimum children nodes to generate for parent', static::$maxChildren)
            ->addOption('depth', null, InputOption::VALUE_REQUIRED, 'Max tree depth', static::$depth);
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $name  = $input->getArgument('name');
        $dir   = $input->getOption('dir');
        $min   = $input->getOption('min');
        $max   = max($min, $input->getOption('max'));
        $depth = $input->getOption('depth');

        $data = $this->makeTree($min, $max, $depth);

        $filename = $dir . DIRECTORY_SEPARATOR . $name;

        file_put_contents($filename, "<?php return " . var_export($data, true) . ";");

        $filename = realpath($filename);
        $size     = filesize($filename);
        $output->writeln("<info>Writed to {$filename}</info>");

    }

    protected function makeTree($min, $max, $depth)
    {
        $items = [];
        if($depth <= 0) {
            return $items;
        }

        for($i = rand($min, $max); $i; $i--) {

            $items[] = [
                'id'       => $this->generator->uuid,
                'name'     => $this->generator->name,
                'value'    => $this->generator->numberBetween(1, 5000000),
                'float'    => $this->generator->randomFloat(2, 10, 5000),
                'children' => $this->makeTree($min, $max, $depth - 1)
            ];

        }

        return $items;
    }

}